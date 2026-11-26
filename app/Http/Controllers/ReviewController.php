<?php
namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $product_id)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // One review per user per product
        $existing = Review::where('user_id', Auth::id())
                          ->where('product_id', $product_id)
                          ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already reviewed this product!');
        }

        $photoName = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . uniqid() . '.' . $photo->extension();
            $photo->move(public_path('uploads/reviews'), $photoName);
        }

        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $product_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'photo'      => $photoName,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    public function destroy($id)
    {
        $review = Review::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        if ($review->photo && file_exists(public_path('uploads/reviews/' . $review->photo))) {
            unlink(public_path('uploads/reviews/' . $review->photo));
        }

        $review->delete();
        return redirect()->back()->with('success', 'Review deleted.');
    }
}