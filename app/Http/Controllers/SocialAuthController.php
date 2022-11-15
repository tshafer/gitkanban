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
            'gitlab' => Socialite::driver($social)
                ->setScopes(['api', 'read_api', 'read_user', 'read_repository', 'write_repository'])
                ->redirect(),
            'bitbucket' => Socialite::driver($social)
                ->setScopes(['repository', 'repository:write', 'pullrequest', 'pullrequest:write', 'project', 'email'])
                ->redirect(),
            default => Socialite::driver($social)->redirect(),
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
            switch ($social) {
                case 'github':
                    $source->update(
                        [
                            'type' => $social,
                            'label' => $socialUser->email,
                            'name' => $socialUser->nickname,
                            'token' => $socialUser->token,
                            'refresh_token' => $socialUser->refreshToken,
                            'expires_in' => $socialUser->expiresIn,
                            'meta_updated_at' => now(),
                            'meta' => [
                                'number_repos' => $socialUser->user['total_private_repos'] + $socialUser->user['public_repos'],
                                'url' => $socialUser->user['url'],
                                'html_url' => $socialUser->user['html_url'],
                                'avatar' => $socialUser->avatar,
                                'gravatar_id' => $socialUser->user['gravatar_id'],
                            ],
                        ]
                    );
                    break;
                case 'gitlab':
                    $source->update([
                        'type' => $social,
                        'label' => $socialUser->email,
                        'name' => $socialUser->nickname,
                        'token' => $socialUser->token,
                        'refresh_token' => $socialUser->refreshToken,
                        'expires_in' => $socialUser->expiresIn,
                        'meta_updated_at' => now(),
                        'meta' => [
                            // 'total_private_repos' => $socialUser->user['total_private_repos'],
                            // 'owned_private_repos' => $socialUser->user['owned_private_repos'],
                            // 'public_repos' => $socialUser->user['public_repos'],
                            'url' => null,
                            'html_url' => $socialUser->web_url,

                            'avatar' => $socialUser->avatar,
                            'gravatar_id' => null,
                        ],
                    ]);
                    $source->refresh();
                    break;
                case 'bitbucket':
                    $source->update([
                        'type' => $social,
                        'label' => $socialUser->email,
                        'name' => $socialUser->nickname,
                        'token' => $socialUser->token,
                        'refresh_token' => $socialUser->refreshToken,
                        'expires_in' => $socialUser->expiresIn,
                        'meta_updated_at' => now(),
                        'meta' => [
                            // 'total_private_repos' => $socialUser->user['total_private_repos'],
                            // 'owned_private_repos' => $socialUser->user['owned_private_repos'],
                            // 'public_repos' => $socialUser->user['public_repos'],
                            'url' => null,
                            'html_url' => $socialUser->web_url,
                            'avatar' => $socialUser->avatar,
                            'gravatar_id' => null,
                        ],
                    ]);
                    $source->refresh();
                    break;
            }
            if ($source->wasRecentlyCreated === true) {
                $text = __(':name was added successfully!', ['name' => ucfirst(__($social))]);
            } else {
                $text = __(':name was updated successfully!', ['name' => ucfirst(__($social))]);
            }

            return redirect()->route('source-providers')->banner($text);
        } catch (Exception $e) {
            return redirect()->route('source-providers')->dangerBanner('There was an error connecting your '.ucfirst(__($social)).' account. Please try again.');
        }
    }

    /**
     * Get user from authentication provider.
     *
     * @return mixed
     */
    private function getUserFromSocial(string $social)
    {
        return match ($social) {
            default => Socialite::driver($social)->user(),
        };
    }
}
