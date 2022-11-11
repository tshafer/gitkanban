<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * @param  Request  $request
     * @param  mixed  $provider
     * @return RedirectResponse
     */
    public function unlinkSocialProvider(Request $request, $provider): RedirectResponse
    {
        $provider = $request->user()->personalTeam()->socialProviders()->where('type', $provider)->first();

        $name = $provider->name;

        $provider->delete();

        return redirect()->route('settings.integrations')->with('success', __($name.' was removed successfully!'));
    }

    /**
     * Redirect user to specified provider in order to complete the authentication process.
     *
     * @return RedirectResponse
     */
    public function redirectToProvider(string $social)
    {
        return match ($social) {
            'github' => Socialite::driver($social)
                ->setScopes(['user', 'admin:public_key', 'admin:repo_hook', 'read:user', 'public_repo', 'repo'])
                ->redirect(),
            default => Socialite::driver($provider)->redirect(),
        };
    }

    /**
     * Handle response authentication provider.
     */
    public function handleProviderCallback(Request $request, string $social): ?RedirectResponse
    {
        try {
            $name = $request->cookie('social_name');

            Cookie::queue(Cookie::forget('social_name'));

            $socialUser = $this->getUserFromSocial($social);

            $team = $request->user()->currentTeam;

            $source = $team->sourceProviders()->firstOrCreate([
                'unique_id' => md5($social.$socialUser->id.$team->id.$socialUser->email.$socialUser->nickname),
            ]);
            $source->update([
                'type' => $social,
                'label' => $socialUser->email,
                'name' => $socialUser->nickname,
                'token' => $socialUser->token,
                'meta' => [
                    'total_private_repos' => $socialUser->user['total_private_repos'],
                    'owned_private_repos' => $socialUser->user['owned_private_repos'],
                    'public_repos' => $socialUser->user['public_repos'],
                    'url' => $socialUser->user['url'],
                    'html_url' => $socialUser->user['html_url'],
                    'refresh_token' => $socialUser->refreshToken,
                    'expires_in' => $socialUser->expiresIn,
                    'avatar' => $socialUser->avatar,
                    'gravatar_id' => $socialUser->user['gravatar_id'],
                ],
            ]);

            return redirect()->route('source-providers')->with('success', __(ucfirst($social).' was connected successfully!'));
        } catch (Exception $e) {
            dd($e->getMessage());
            // return redirect()->to('/settings/providers')->with(
            //     'error',
            //     'There was an error connecting your '.__($provider).' account. Please try again.'
            // );
        }
    }

    /**
     * Get user from authentication provider.
     *
     * @return SocialUser
     */
    private function getUserFromSocial(string $social)
    {
        return match ($social) {
            'bitbucket', 'gitlab' => Socialite::driver($social)
                ->stateless()
                ->user(),
            default => Socialite::driver($social)->user(),
        };
    }
}
