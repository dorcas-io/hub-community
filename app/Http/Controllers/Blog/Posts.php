<?php

namespace App\Http\Controllers\Blog;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Exceptions\DeletingFailedException;
use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Posts extends Controller
{
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newPost(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] = 'New Post | BlogAdmin';
        $this->data['page']['header'] = ['title' => 'New Post | Blog'];
        $this->data['breadCrumbs'] = [
            'crumbs' => [
                ['text' => 'Home', 'href' => route('blog')],
                ['text' => 'New Post', 'href' => route('blog.admin.new-post'), 'isActive' => true],
            ]
        ];
    
        $this->setViewUiResponse($request);
        $this->data['categories'] = $this->getBlogCategories($sdk);
        return view('blog.new-post', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPost(Request $request, Sdk $sdk)
    {
        $rules = [
            'title' => 'required|string|max:80',
            'summary' => 'required|string',
            'categories' => 'nullable|array',
            'categories.*' => 'required_with:categories|string',
            'is_published' => 'nullable|numeric|in:0,1',
            'publish_at' => 'nullable|date_format:"d-m-Y H:i"'
        ];
        $this->getValidationFactory()->make($request->all(), $rules, [], [])->validate();
        # validate the request
        try {
            $postId = $request->has('post_id') ? $request->input('post_id') : null;
            $resource = $sdk->createBlogResource();
            $payload = $request->only(['title', 'summary', 'content', 'retain_photo']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            if ($request->has('categories')) {
                $categories = $request->input('categories', []);
                if ($request->has('image')) {
                    foreach ($categories as $id) {
                        $resource->addMultipartParam('categories[]', $id);
                    }
                } else {
                    $resource->addBodyParam('categories', $categories);
                }
            }
            if ($request->has('publish_at') && !empty($request->input('publish_at'))) {
                $date = Carbon::createFromFormat('d-m-Y H:i', $request->input('publish_at'));
                $resource->addBodyParam('publish_at', $date->format('d/m/Y H:i'));
                $resource->addBodyParam('is_published', 0);
            } else {
                $resource->addBodyParam('is_published', 1);
            }
            if ($request->has('image')) {
                $file = $request->file('image');
                $resource->addMultipartParam('image', file_get_contents($file->getRealPath(), false), $file->getClientOriginalName());
            }
            $response = $resource->send('post', ['posts', !empty($postId) ? $postId : '']);
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while '. (empty($postId) ? 'adding' : 'updating') .' the blog post. '.$message);
            }
            $response = (bootstrap_ui_html_response(['Successfully '. (empty($postId) ? 'added' : 'updated the') .' blog post.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (bootstrap_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function editPost(Request $request, Sdk $sdk, string $id)
    {
        $this->data['page']['title'] = 'Edit Post | BlogAdmin';
        $this->data['page']['header'] = ['title' => 'Edit Post | Blog'];
        $this->data['breadCrumbs'] = [
            'crumbs' => [
                ['text' => 'Home', 'href' => route('blog')],
                ['text' => 'Edit Post', 'href' => route('blog.admin.new-post'), 'isActive' => true],
            ]
        ];
    
        $this->setViewUiResponse($request);
        $this->data['categories'] = $this->getBlogCategories($sdk);
        $this->data['post'] = $this->getPost($sdk, $id);
        return view('blog.new-post', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePost(Request $request, Sdk $sdk, string $id)
    {
        $request->request->set('post_id', $id);
        return $this->createPost($request, $sdk);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePostXhr(Request $request, Sdk $sdk, string $id)
    {
        $query = $sdk->createBlogResource()->send('DELETE', ['posts', $id]);
        if (!$query->isSuccessful()) {
            // do something here
            throw new DeletingFailedException($query->errors[0]['title'] ?? 'Could not delete the selected post.');
        }
        return response()->json($query->getData());
    }
    
    /**
     * @param Sdk    $sdk
     * @param string $id
     *
     * @return array|mixed|object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getPost(Sdk $sdk, string $id)
    {
        $response = $sdk->createBlogResource()->send('get', ['posts', $id]);
        if (!$response->isSuccessful()) {
            throw new DeletingFailedException($query->errors[0]['title'] ?? 'Could not load the selected post.');
        }
        return $response->getData(true);
    }
}
