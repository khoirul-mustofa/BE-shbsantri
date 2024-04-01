<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Response\CustomsResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'size' => 'integer|min:1', // Number of items per page
                'page' => 'integer|min:1', // Current page number
                'search' => 'string', // Search query
                'category_id' => 'integer', // Category ID for filtering
                'user_id' => 'integer', // User ID for filtering
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return CustomsResponse::error(
                    $validator->errors(),
                    'Validation Error.'
                );
            }

            // Retrieve pagination parameters from the request
            $perPage = $request->query('size', 10); // Default: 10 items per page
            $page = $request->query('page', 1); // Default: first page
            $search = $request->query('search');
            $categoryId = $request->query('category_id');
            $userId = $request->query('user_id');

            // Retrieve news query with related category and user
            $query = News::latest()->with('category', 'user');

            // Apply search filter if search query is provided
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('content', 'like', "%$search%");
                });
            }

            // Apply category filter if category ID is provided
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            // Apply user filter if user ID is provided
            if ($userId) {
                $query->where('user_id', $userId);
            }

            // Paginate the news
            $news = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform news data to match the desired structure
            $transformedNews = $news->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'video' => $item->video,
                    'category' => [
                        'id' => $item->category->id,
                        'name' => $item->category->name,
                    ],
                    'user' => [
                        'id' => $item->user->id,
                        'name' => $item->user->name,
                        'avatar' => $item->user->avatar, // Fix this attribute name to 'avatar'
                    ],
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            // Construct the response
            $response = [
                'status' => 200,
                'message' => 'Success',
                'count' => $transformedNews->count(),
                'data' => $transformedNews,
                'page' => $news->currentPage(), // Current page number
                'size' => $news->perPage(), // Number of items per page
                'total_pages' => $news->lastPage(), // Total number of pages
                'total_data' => $news->total(), // Total number of data
            ];

            // Menambahkan informasi halaman selanjutnya
            if ($news->hasMorePages()) {
                $response['next_page'] = $news->currentPage() + 1;
            }

            // Menambahkan informasi halaman sebelumnya
            if ($news->currentPage() > 1) {
                $response['previous_page'] = $news->currentPage() - 1;
            }

            // Return JSON response
            return response()->json($response);

    }




    public function store(Request $request): JsonResponse
    {
        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:news',
            'content' => 'required|string',
            'video' => 'nullable|string',
            'image' => 'nullable|string',
            'pdf' => 'nullable|string',
            'ppt' => 'nullable|string',
            'category_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return  CustomsResponse::error(
                $validator->errors(),
                'Validation Error.',
                400
            );
        }

        // Buat instance baru dari model News dan langsung simpan ke dalam penyimpanan
        $news = News::create($request->all());

        // Berikan respons bahwa data berhasil disimpan

        return CustomsResponse::success(
            $news,
            'News created successfully.',
            201
        );
    }

    public function show(News $news): JsonResponse
    {
        $data = News::find($news->id);

        if (!$data) {
            return CustomsResponse::error(
                null,
                'News not found.',
                404
            );
        }
        return CustomsResponse::success(
            $data,
            'News retrieved successfully.',
            200
        );
    }




    public function update(Request $request, News $news): JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'content' => 'string',
            'video' => 'string',
            'image' => 'string',
            'pdf' => 'string',
            'category_id' => 'integer',
            'user_id' => 'integer',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return CustomsResponse::error(
                $validator->errors(),
                'Validation Error.',
                400
            );
        }

        // Update the news with the new data
        $news->update($request->all());

        // Return success response
        return CustomsResponse::success(
            $news,
            'News updated successfully.',
            200
        );
    }



    public function destroy($id): JsonResponse
    {
        try {
            // Cari berita yang ditemukan
            $news = News::findOrFail($id);
            // Hapus berita yang ditemukan
            $news->delete();

            // Beri respons sukses jika berhasil menghapus
            return CustomsResponse::success(
                null,
                'News deleted successfully.',
                200
            );
        } catch (\Exception $e) {
            return CustomsResponse::error(
                $e->getMessage(),
                'News not found.',
                404
            );
        }
    }
}
