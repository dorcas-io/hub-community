<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\ECommerce\OnlineStore;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    /**
     * Home constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Blog';
        $this->data['page']['header'] = ['title' => 'Blog'];
    }
    
    /**
     * @param Request     $request
     * @param string|null $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, string $slug = null)
    {
        $blogOwner = $this->getCompanyViaDomain();
        # get the store owner
        if (empty($blogOwner)) {
            abort(404, 'Could not find a blog at this URL.');
        }
        $this->data['categorySlug'] = $slug;
        $this->data['defaultSearch'] = $request->get('q', '');
        $this->data['blogOwner'] = $blogOwner;

        if ($request->session()->has('dorcas_referrer')) {
            $referrer =  $request->session()->get('dorcas_referrer', ["mode" => "", "value" => ""]);
            $this->data['page']['title'] = strtoupper($referrer["value"]) . "'s " . $this->data['page']['title'];
            $this->data['page']['header']['title'] = strtoupper($referrer["value"]) . "'s Blog";
            $this->data['blogName'] = strtoupper($referrer["value"]) . "'s Blog";
        } else {
            $this->data['page']['title'] = $blogOwner->name . ' ' . $this->data['page']['title'];
            $this->data['page']['header']['title'] = $blogOwner->name . ' '  . $this->data['page']['title'];
            $this->data['blogName'] = $blogOwner->name . " Blog";
        }

        return view('blog.timeline', $this->data);
    }
    
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categories()
    {
        return redirect()->route('blog');
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postDetails(Request $request, Sdk $sdk, string $id)
    {
        $this->data['breadCrumbs'] = [
            'crumbs' => [
                ['text' => 'Home', 'href' => route('blog')],
                ['text' => 'Posts', 'href' => route('blog.posts')],
                ['text' => 'Reading', 'href' => route('blog.posts.details', [$id])],
            ]
        ];
        $blogOwner = $this->getCompanyViaDomain();
        # get the blog owner
        if (empty($blogOwner)) {
            abort(404, 'Could not find a blog at this URL.');
        }
        $query = $sdk->createBlogResource($blogOwner->id)->addQueryArgument('slug', $id)->send('GET', ['posts']);
        if (!$query->isSuccessful()) {
            abort(500, $query->getErrors()[0]['title'] ?? 'Something went wrong while fetching the blog post.');
        }
        $this->data['post'] = $post = $query->getData(true);
        $this->data['page']['title'] = $post->title . ' | Blog';
        $this->data['page']['header']['title'] = $post->title;
        return view('blog.post-details', $this->data);
    }
}
