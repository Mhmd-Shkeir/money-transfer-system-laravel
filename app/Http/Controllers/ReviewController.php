<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    
    public function index()
    {
        $reviews = Review::with('user')->orderBy('created_at', 'desc')->paginate(10);
        $userReview = Review::where('user_id', Auth::id())->first();
        $averageRating = Review::avg('rating');
        $totalReviews = Review::count();
        
        return view('user.reviews', compact('reviews', 'userReview', 'averageRating', 'totalReviews'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ], [
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'comment.required' => 'Please write a review.',
            'comment.min' => 'Review must be at least 10 characters.',
            'comment.max' => 'Review cannot exceed 1000 characters.',
        ]);

        $existingReview = Review::where('user_id', Auth::id())->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $message = 'Your review has been updated successfully!';
        } else {
            Review::create([
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $message = 'Your review has been posted successfully!';
        }

        return redirect()->route('user.reviews')->with('success', $message);
    }

    
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $user = Auth::user();

        if ($review->user_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $review->delete();

        return redirect()->route('user.reviews')->with('success', 'Review deleted successfully!');
    }
}
