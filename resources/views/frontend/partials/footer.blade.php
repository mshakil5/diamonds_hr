@php
    use App\Models\CompanyDetails;
    $company = CompanyDetails::select('footer_content', 'company_name', 'facebook', 'twitter', 'linkedin')->first();
    $recentPosts = App\Models\Blog::where('status', '1')->orderBy('id', 'desc')->select('title', 'created_at', 'slug')->limit(2)->get();
@endphp
<footer id="footer" class="footer light-background">
    <div class="container">
        <div class="row g-4">
        <div class="col-md-6 col-lg-3 mb-3 mb-md-0">
            <div class="widget">
                <h3 class="widget-heading">{{ $company->company_name }}</h3>
                <p class="mb-4">
                    {!! $company->footer_content !!}
                </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 ps-lg-5 mb-3 mb-md-0">
            <div class="widget">
            <h3 class="widget-heading">Navigation</h3>
                <ul class="list-unstyled float-start me-5">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 pl-lg-5">
            <div class="widget">
                <h3 class="widget-heading">Recent Posts</h3>
                <ul class="list-unstyled footer-blog-entry">
                    @foreach ($recentPosts as $post)
                        <li>
                            <span class="d-block date">{{ $post->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('blog.details', $post->slug) }}">{{ $post->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 pl-lg-5">
            <div class="widget">
            <h3 class="widget-heading">Connect</h3>
            <ul class="list-unstyled social-icons light mb-3">
                <li>
                <a href="{{ $company->facebook }}"><span class="bi bi-facebook"></span></a>
                </li>
                <li>
                <a href="{{ $company->twitter }}"><span class="bi bi-twitter-x"></span></a>
                </li>
                <li>
                <a href="{{ $company->linkedin }}"><span class="bi bi-linkedin"></span></a>
                </li>
                <li>
            </ul>
            </div>
        </div>
        </div>
    </div>
</footer>