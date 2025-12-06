@extends('layouts.dashboard')
@section('title', 'Reviews & Ratings')
@section('page-title', 'Reviews & Ratings')
@section('page-subtitle', 'Share your experience with our service')

@section('content')
<!-- Statistics Row -->
<div class="row mb-4">
    <!-- Average Rating Card -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; color: #FFC107; margin-bottom: 10px;">
                    <i class="fas fa-star"></i>
                </div>
                <h6 class="card-title text-muted">Average Rating</h6>
                <p class="mb-1" style="font-size: 2rem; font-weight: bold; color: #0077B6;">{{ number_format($averageRating ?? 0, 1) }}</p>
                <small class="text-muted">out of 5 stars</small>
            </div>
        </div>
    </div>

    <!-- Total Reviews Card -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; color: #06D6A0; margin-bottom: 10px;">
                    <i class="fas fa-comments"></i>
                </div>
                <h6 class="card-title text-muted">Total Reviews</h6>
                <p class="mb-1" style="font-size: 2rem; font-weight: bold; color: #0077B6;">{{ $totalReviews ?? 0 }}</p>
                <small class="text-muted">from users</small>
            </div>
        </div>
    </div>

    <!-- Your Rating Card -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="fas fa-user-circle me-2" style="color: #0077B6;"></i>
                    Your Rating
                </h6>
                @if($userReview)
                    <div class="mb-2">
                        <span style="font-size: 1.5rem;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $userReview->rating ? 's' : 'r' }} fa-star" style="color: #FFC107;"></i>
                            @endfor
                        </span>
                    </div>
                    <p class="mb-2 small">{{ $userReview->comment }}</p>
                    <small class="text-muted">Posted on {{ $userReview->created_at->format('M d, Y') }}</small>
                @else
                    <p class="text-muted mb-0 small">You haven't posted a review yet. Share your feedback below!</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Review Form Card -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-pencil-alt me-2"></i>
            {{ $userReview ? 'Update Your Review' : 'Leave Your Review' }}
        </h5>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('user.reviews.store') }}">
            @csrf

            <!-- Rating Selection -->
            <div class="mb-4">
                <label class="form-label fw-bold">Your Rating <span class="text-danger">*</span></label>
                <div class="rating-selection mb-3">
                    <div class="d-flex gap-3 flex-wrap">
                        @for($i = 5; $i >= 1; $i--)
                            <div class="form-check">
                                <input class="form-check-input rating-input" 
                                       type="radio" 
                                       name="rating" 
                                       id="rating{{ $i }}" 
                                       value="{{ $i }}"
                                       {{ $userReview && $userReview->rating == $i ? 'checked' : '' }}
                                       required
                                       style="width: 30px; height: 30px; cursor: pointer; accent-color: #FFC107;">
                                <label class="form-check-label ms-2" for="rating{{ $i }}" style="font-size: 1.5rem; cursor: pointer;">
                                    <i class="far fa-star" style="color: #FFC107;"></i>
                                </label>
                            </div>
                        @endfor
                    </div>
                    <small class="text-muted d-block mt-3">
                        <strong>Rating:</strong> <span id="ratingText">Select a rating</span>
                    </small>
                </div>
                @error('rating')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Review Comment -->
            <div class="mb-4">
                <label class="form-label fw-bold">Your Review <span class="text-danger">*</span></label>
                <textarea name="comment" 
                          class="form-control @error('comment') is-invalid @enderror" 
                          rows="5" 
                          placeholder="Share your experience with our money transfer service..."
                          required>{{ $userReview?->comment ?? '' }}</textarea>
                <small class="text-muted d-block mt-1">
                    <span id="charCount">0</span>/1000 characters
                </small>
                @error('comment')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>
                    {{ $userReview ? 'Update Review' : 'Post Review' }}
                </button>
                @if($userReview)
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>
                        Delete Review
                    </button>
                @endif
            </div>
        </form>

        <!-- Hidden Delete Form -->
        @if($userReview)
            <form id="deleteForm" method="POST" action="{{ route('user.reviews.destroy', $userReview->id) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>

<!-- All Reviews Card -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-list me-2" style="color: #0077B6;"></i>
            All Reviews ({{ $totalReviews ?? 0 }})
        </h5>
    </div>
    <div class="card-body">
        @if ($reviews->count() > 0)
            <div class="reviews-list">
                @foreach ($reviews as $review)
                    <div class="review-item mb-3 pb-3 @if(!$loop->last)border-bottom @endif">
                        <!-- Review Header -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #0077B6; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                                    {{ substr($review->user->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0" style="font-size: 0.95rem;">{{ $review->user->first_name }} {{ $review->user->last_name }}</h6>
                                    <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                            @if(Auth::check() && (Auth::id() === $review->user_id || Auth::user()->role === 'admin'))
                                <form method="POST" action="{{ route('user.reviews.destroy', $review->id) }}" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this review?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Rating Stars -->
                        <div class="mb-2">
                            <span style="font-size: 1rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star" style="color: #FFC107;"></i>
                                @endfor
                            </span>
                            <span class="badge bg-warning text-dark ms-2">{{ $review->rating }}/5</span>
                        </div>

                        <!-- Review Comment -->
                        <p class="mb-0 text-break" style="font-size: 0.95rem;">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($reviews->hasPages())
                <nav aria-label="Page navigation" class="mt-4">
                    {{ $reviews->links() }}
                </nav>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-star" style="font-size: 3rem; color: #DDD; margin-bottom: 15px; display: block;"></i>
                <p class="text-muted">No reviews yet. Be the first to share your feedback!</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Rating selection functionality
    const ratingInputs = document.querySelectorAll('.rating-input');
    const ratingText = document.getElementById('ratingText');
    const charCount = document.getElementById('charCount');
    const commentTextarea = document.querySelector('textarea[name="comment"]');

    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const rating = this.value;
            const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            ratingText.textContent = ratingLabels[rating];
            updateStars();
        });
    });

    function updateStars() {
        const checkedRating = document.querySelector('.rating-input:checked');
        const allStars = document.querySelectorAll('.rating-selection .fa-star');
        
        allStars.forEach(star => {
            star.classList.remove('fas');
            star.classList.add('far');
        });
        
        if (checkedRating) {
            const rating = parseInt(checkedRating.value);
            const starsArray = Array.from(allStars).reverse();
            for (let i = 0; i < rating; i++) {
                starsArray[i].classList.remove('far');
                starsArray[i].classList.add('fas');
            }
        }
    }

    // Character counter
    if (commentTextarea) {
        commentTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Delete confirmation
    function confirmDelete() {
        if (confirm('Are you sure you want to delete your review?')) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Initialize on page load
    window.addEventListener('load', function() {
        updateStars();
        if (commentTextarea) {
            charCount.textContent = commentTextarea.value.length;
        }
    });
</script>
@endpush

@push('styles')
<style>
    .review-item {
        transition: background-color 0.2s;
    }

    .review-item:hover {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
    }

    .rating-selection .form-check-label {
        margin-bottom: 0;
    }

    .rating-selection .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
</style>
@endpush

@endsection

