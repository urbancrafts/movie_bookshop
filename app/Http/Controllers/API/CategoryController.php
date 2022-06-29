<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    //
    public function create_category(Request $request)
    {
        // `name`, `description`, `slug`, `status`,
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }

        $name = $request->name;
        $description = $request->description;
        $slug = Str::of($name)->slug('-');

        $save_category = Category::create([
            'name' => $name,
            'description' => $description,
            'slug' => $slug
        ]);

        if (!$save_category) {
            return response_data(false, 422, "Sorry! we could not create the category", false, false, false);
        }

        return response_data(true, 200, "Category created successfully", ['values' => $save_category], false, false);
    }

    public function fetch_all_category()
    {
        $all_categories = Category::all();

        if (!$all_categories) {
            return response_data(false, 422, 'Sorry We could not fetch all the categories', false, false, false);
        }

        return response_data(true, 200, 'All Categories fetched successfully.', ['values' => $all_categories], false, false);
    }

    public function fetch_one_category($id)
    {
        $fetch_one_category = Category::find($id);

        if (!$fetch_one_category) {
            return response_data(false, 422, 'Sorry We could not fetch the category', false, false, false);
        }

        return response_data(true, 200, 'Category fetched successfully.', ['values' => $fetch_one_category], false, false);
    }

    public function edit_category(Request $request)
    {
        // `name`, `description`, `slug`, `status`,
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }

        $id = $request->id;
        $name = $request->name;
        $description = $request->description;

        $find_category = Category::find($id);

        if (!$find_category) {
            return response_data(false, 422, 'Sorry We could not fetch the category', false, false, false);
        }

        $update_category = Category::where('id', $id)->update([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'status' => isset($request->status) ? $request->status : $find_category->status
        ]);

        if (!$update_category) {
            return response_data(false, 422, 'Sorry We could not update the category', false, false, false);
        }

        return response_data(true, 200, 'Category updated successfully.', false, false, false);
    }

    public function delete_category($id)
    {
        $find_category = Category::find($id);

        if (!$find_category) {
            return response_data(false, 422, 'Sorry We could not fetch the category', false, false, false);
        }

        $delete_category = $find_category->delete();

        if(!$delete_category){
            return response_data(false, 422, 'Sorry We could not delete the category', false, false, false);
        }

        return response_data(true, 200, 'Category deleted successfully.', false, false, false);
    }
}
