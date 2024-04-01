<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Response\CustomsResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Retrieve all categories
        $categories = Category::all();

        // Return JSON response using CustomsResponse helper
        return CustomsResponse::success($categories, 'Categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return CustomsResponse::error($validator->errors(), 'Validation error.', 400);
        }

        // Create new category
        $category = Category::create([
            'name' => $request->name,
        ]);

        // Return JSON response with success message
        return CustomsResponse::success($category, 'Category created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        // Find the category by ID
        $category = Category::find($id);

        // Check if category exists
        if (!$category) {
            return CustomsResponse::error(null, 'Category not found.', 404);
        }

        // Return JSON response with the category data
        return CustomsResponse::success($category, 'Category retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Find the category by ID
        $category = Category::find($id);

        // Check if category exists
        if (!$category) {
            return CustomsResponse::error(null, 'Category not found.', 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name,' . $id,
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return CustomsResponse::error($validator->errors(), 'Validation error.', 400);
        }

        // Update the category
        $category->update([
            'name' => $request->name,
        ]);

        // Return JSON response with success message
        return CustomsResponse::success($category, 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        // Find the category by ID
        $category = Category::find($id);

        // Check if category exists
        if (!$category) {
            return CustomsResponse::error(null, 'Category not found.', 404);
        }

        // Delete the category
        $category->delete();

        // Return JSON response with success message
        return CustomsResponse::success(null, 'Category deleted successfully.');
    }
}
