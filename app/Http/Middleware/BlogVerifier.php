<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ECommerce\Blog\Dashboard;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BlogVerifier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('domain')) {
            # not accessing it via a custom subdomain that has been resolved
            abort(404, 'No blog at this URL');
        }
        $domain = $request->session()->get('domain');
        # get the domain
        $user = $request->user();
        $blogAdministrator = null;
        if (!empty($domain->owner['data'])) {
            $blogOwner = (object) $domain->owner['data'];
            $blogAdministrator = !empty($user) && $user->company['data']['id'] === $blogOwner->id ? $user : null;
            $firstUser = !empty($blogOwner->users['data']) && !empty($blogOwner->users['data'][0]) ?
                (object) $blogOwner->users['data'][0] : null;
            # get the first user account
            $partner = !empty($firstUser) && !empty($firstUser->partner['data']) ?
                (object) $firstUser->partner['data'] : null;
            # get the partner information
            View::composer('blog.*', function ($view) use (&$blogAdministrator, $domain, $blogOwner, $partner) {
                $view->with('blogOwner', $blogOwner);
                $blogDomain = 'https://'.$domain->prefix . '.' . $domain->domain['data']['domain'].'/blog';
                $view->with('blogDomain', $blogDomain);
                $settings = Dashboard::getBlogSettings((array) $blogOwner->extra_data);
                $view->with('blogSettings', $settings);
                $defaultBlogName = $blogOwner->name . ' Blog';
                $view->with('blogName', !empty($settings['blog_name']) ? $settings['blog_name'] : $defaultBlogName);
                if (!empty($blogAdministrator)) {
                    $view->with('blogAdministrator', $blogAdministrator);
                }
                if (!empty($partner)) {
                    $view->with('partnerHubConfig', $partner->extra_data['hubConfig'] ?? []);
                }
            });
            $request->session()->put('blogAdministrator', true);
        }
        if (starts_with($request->path(), 'blog-admin') && empty($blogAdministrator)) {
            $request->session()->remove('blogAdministrator');
            return redirect()->route('blog');
        }
        return $next($request);
    }
}
