<?php

namespace App\Http\Controllers\ECommerce\Blog;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewPost extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'New Blog Post';
        $this->data['page']['header'] = ['title' => 'New Post'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'ECommerce', 'href' => route('apps.ecommerce')],
                ['text' => 'Posts', 'href' => route('apps.ecommerce.blog')],
                ['text' => 'New Posts', 'href' => route('apps.ecommerce.blog.new'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'ecommerce';
        $this->data['selectedSubMenu'] = 'posts';
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['categories'] = $this->getBlogCategories($sdk);
        return view('blog.admin.new-post', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'title' => 'required|string|max:80',
            'summary' => 'required|string',
            'content' => 'required|numeric',
            'categories' => 'nullable|array',
            'categories.*' => 'required_with:categories|string',
            'image' => 'nullable|image|max:4096',
            'is_published' => 'nullable',
            'publish_at' => 'required_without:is_published|date_format:"d F, Y"'
        ]);
        # validate the request
        try {
            $resource = $sdk->createBlogResource();
            $payload = $request->only(['title', 'summary', 'content', 'categories']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $resource->addBodyParam('is_published', $request->has('is_published') ? 1 : 0);
            if ($request->has('publish_at')) {
                $date = Carbon::createFromFormat('d F, Y', $request->input('publish_at'));
                $resource->addBodyParam('publish_at', $date->format('d/m/Y H:i'));
            }
            if ($request->has('image')) {
                $file = $request->file('image');
                $resource->addMultipartParam('image', file_get_contents($file->getRealPath()), $file->getClientOriginalName());
            }
            $response = $resource->send('post', ['posts']);
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the blog post. '.$message);
            }
            $response = (material_ui_html_response(['Successfully added blog post.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
