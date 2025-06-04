@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row">

    <div class="col-lg-8">

        <section id="blog-details" class="blog-details section">
        <div class="container">

            <article class="article">

            <div class="post-img">
                <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="img-fluid">
            </div>

            <h2 class="title">{{ $blog->title }}</h2>

            <div class="meta-top">
                <ul>
                <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a>{{ $blog->user->name }}</a></li>
                <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a><time datetime="{{ $blog->created_at->format('Y-m-d') }}">{{ $blog->created_at->format('M d, Y') }}</time></a></li>
                <li class="d-flex align-items-center d-none"><i class="bi bi-chat-dots"></i> <a href="blog-details.html">12 Comments</a></li>
                </ul>
            </div>

            <div class="content">
                {!! $blog->description !!}
            </div>

            <div class="meta-bottom">
                <i class="bi bi-folder"></i>
                <ul class="cats">
                <li><a>{{ $blog->category->name ?? '' }}</a></li>
                </ul>
            </div>

            </article>

        </div>
        </section>

        <!-- Blog Comments Section -->
        <section id="blog-comments" class="blog-comments section d-none">

        <div class="container">

            <h4 class="comments-count">8 Comments</h4>

            <div id="comment-1" class="comment">
            <div class="d-flex">
                <div class="comment-img"><img src="assets/img/blog/comments-1.jpg" alt=""></div>
                <div>
                <h5><a href="">Georgia Reader</a> <a href="#" class="reply"><i class="bi bi-reply-fill"></i> Reply</a></h5>
                <time datetime="2020-01-01">01 Jan,2022</time>
                <p>
                    Et rerum totam nisi. Molestiae vel quam dolorum vel voluptatem et et. Est ad aut sapiente quis molestiae est qui cum soluta.
                    Vero aut rerum vel. Rerum quos laboriosam placeat ex qui. Sint qui facilis et.
                </p>
                </div>
            </div>
            </div><!-- End comment #1 -->

            <div id="comment-2" class="comment">
            <div class="d-flex">
                <div class="comment-img"><img src="" alt=""></div>
                <div>
                <h5><a href="">Aron Alvarado</a> <a href="#" class="reply"><i class="bi bi-reply-fill"></i> Reply</a></h5>
                <time datetime="2020-01-01">01 Jan,2022</time>
                <p>
                    Ipsam tempora sequi voluptatem quis sapiente non. Autem itaque eveniet saepe. Officiis illo ut beatae.
                </p>
                </div>
            </div>

            <div id="comment-reply-1" class="comment comment-reply">
                <div class="d-flex">
                <div class="comment-img"><img src="" alt=""></div>
                <div>
                    <h5><a href="">Lynda Small</a> <a href="#" class="reply"><i class="bi bi-reply-fill"></i> Reply</a></h5>
                    <time datetime="2020-01-01">01 Jan,2022</time>
                    <p>
                    Enim ipsa eum fugiat fuga repellat. Commodi quo quo dicta. Est ullam aspernatur ut vitae quia mollitia id non. Qui ad quas nostrum rerum sed necessitatibus aut est. Eum officiis sed repellat maxime vero nisi natus. Amet nesciunt nesciunt qui illum omnis est et dolor recusandae.

                    Recusandae sit ad aut impedit et. Ipsa labore dolor impedit et natus in porro aut. Magnam qui cum. Illo similique occaecati nihil modi eligendi. Pariatur distinctio labore omnis incidunt et illum. Expedita et dignissimos distinctio laborum minima fugiat.

                    Libero corporis qui. Nam illo odio beatae enim ducimus. Harum reiciendis error dolorum non autem quisquam vero rerum neque.
                    </p>
                </div>
                </div>

                <div id="comment-reply-2" class="comment comment-reply">
                <div class="d-flex">
                    <div class="comment-img"><img src="" alt=""></div>
                    <div>
                    <h5><a href="">Sianna Ramsay</a> <a href="#" class="reply"><i class="bi bi-reply-fill"></i> Reply</a></h5>
                    <time datetime="2020-01-01">01 Jan,2022</time>
                    <p>
                        Et dignissimos impedit nulla et quo distinctio ex nemo. Omnis quia dolores cupiditate et. Ut unde qui eligendi sapiente omnis ullam. Placeat porro est commodi est officiis voluptas repellat quisquam possimus. Perferendis id consectetur necessitatibus.
                    </p>
                    </div>
                </div>

                </div><!-- End comment reply #2-->

            </div><!-- End comment reply #1-->

            </div><!-- End comment #2-->

            <div id="comment-3" class="comment">
            <div class="d-flex">
                <div class="comment-img"><img src="" alt=""></div>
                <div>
                <h5><a href="">Nolan Davidson</a> <a href="#" class="reply"><i class="bi bi-reply-fill"></i> Reply</a></h5>
                <time datetime="2020-01-01">01 Jan,2022</time>
                <p>
                    Distinctio nesciunt rerum reprehenderit sed. Iste omnis eius repellendus quia nihil ut accusantium tempore. Nesciunt expedita id dolor exercitationem aspernatur aut quam ut. Voluptatem est accusamus iste at.
                    Non aut et et esse qui sit modi neque. Exercitationem et eos aspernatur. Ea est consequuntur officia beatae ea aut eos soluta. Non qui dolorum voluptatibus et optio veniam. Quam officia sit nostrum dolorem.
                </p>
                </div>
            </div>

            </div><!-- End comment #3 -->

            <div id="comment-4" class="comment">
            <div class="d-flex">
                <div class="comment-img"><img src="" alt=""></div>
                <div>
                <h5><a href="">Kay Duggan</a> <a href="#" class="reply"><i class="bi bi-reply-fill"></i> Reply</a></h5>
                <time datetime="2020-01-01">01 Jan,2022</time>
                <p>
                    Dolorem atque aut. Omnis doloremque blanditiis quia eum porro quis ut velit tempore. Cumque sed quia ut maxime. Est ad aut cum. Ut exercitationem non in fugiat.
                </p>
                </div>
            </div>

            </div><!-- End comment #4 -->

        </div>

        </section><!-- /Blog Comments Section -->

        <!-- Comment Form Section -->
        <section id="comment-form" class="comment-form section d-none">
        <div class="container">

            <form action="">

            <h4>Post Comment</h4>
            <p>Your email address will not be published. Required fields are marked * </p>
            <div class="row">
                <div class="col-md-6 form-group">
                <input name="name" type="text" class="form-control" placeholder="Your Name*">
                </div>
                <div class="col-md-6 form-group">
                <input name="email" type="text" class="form-control" placeholder="Your Email*">
                </div>
            </div>
            <div class="row">
                <div class="col form-group">
                <input name="website" type="text" class="form-control" placeholder="Your Website">
                </div>
            </div>
            <div class="row">
                <div class="col form-group">
                <textarea name="comment" class="form-control" placeholder="Your Comment*"></textarea>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </div>

            </form>

        </div>
        </section><!-- /Comment Form Section -->

    </div>

    <div class="col-lg-4 sidebar">

        <div class="widgets-container">

        <!-- Blog Author Widget -->
        <div class="blog-author-widget widget-item d-none">

            <div class="d-flex flex-column align-items-center">
            <img src="" class="rounded-circle flex-shrink-0" alt="">
            <h4>Jane Smith</h4>
            <div class="social-links">
                <a href="https://x.com/#"><i class="bi bi-twitter-x"></i></a>
                <a href="https://facebook.com/#"><i class="bi bi-facebook"></i></a>
                <a href="https://instagram.com/#"><i class="biu bi-instagram"></i></a>
                <a href="https://instagram.com/#"><i class="biu bi-linkedin"></i></a>
            </div>

            <p>
                Itaque quidem optio quia voluptatibus dolorem dolor. Modi eum sed possimus accusantium. Quas repellat voluptatem officia numquam sint aspernatur voluptas. Esse et accusantium ut unde voluptas.
            </p>

            </div>
        </div><!--/Blog Author Widget -->

        <div class="search-widget widget-item d-none">
            <h3 class="widget-title">Search</h3>
            <form id="search-form">
                <input type="text" id="search-input" placeholder="Search for blog posts...">
                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
            </form>
            <div id="search-results"></div>
        </div>


        @if($recentBlogs->count() > 0)
        <div class="recent-posts-widget widget-item">
            <h3 class="widget-title">Recent Posts</h3>

            @foreach ($recentBlogs as $recentBlog)
                <div class="post-item">
                    <h4><a href="{{ route('blog.details', $recentBlog->slug) }}">{{ $recentBlog->title }}</a></h4>
                    <time datetime="{{ $recentBlog->created_at->format('Y-m-d') }}">{{ $recentBlog->created_at->format('M d, Y') }}</time>
                </div>
            @endforeach

        </div>
        @endif


        <!-- Tags Widget -->
        <div class="tags-widget widget-item d-none">

            <h3 class="widget-title">Tags</h3>
            <ul>
            <li><a href="#">App</a></li>
            <li><a href="#">IT</a></li>
            <li><a href="#">Business</a></li>
            <li><a href="#">Mac</a></li>
            <li><a href="#">Design</a></li>
            <li><a href="#">Office</a></li>
            <li><a href="#">Creative</a></li>
            <li><a href="#">Studio</a></li>
            <li><a href="#">Smart</a></li>
            <li><a href="#">Tips</a></li>
            <li><a href="#">Marketing</a></li>
            </ul>

        </div><!--/Tags Widget -->

        </div>

    </div>

    </div>
</div>

@endsection