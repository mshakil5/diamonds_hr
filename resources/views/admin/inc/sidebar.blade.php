<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
     
      <li class="nav-item">
        <a href="{{route('admin.dashboard')}}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-chart-line"></i>
          <p>
            Dashboard
          </p>
        </a>
      </li>

      <li class="nav-item d-none">
        <a href="{{route('alladmin')}}" class="nav-link {{ (request()->is('admin/new-admin*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Admin
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('allUsers')}}" class="nav-link {{ (request()->is('admin/users*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Users
          </p>
        </a>
      </li>

      <li class="nav-item dropdown {{ request()->is('admin/blogs*') || request()->is('admin/blog-categories*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/blogs*') || request()->is('admin/blog-categories*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-blog"></i>
              <p>
                  Blogs <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a href="{{ route('allBlogs') }}" class="nav-link {{ request()->routeIs('allBlogs') ? 'active' : '' }}">
                      <i class="fas fa-list nav-icon"></i>
                      <p>All Blogs</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('allBlogCategories') }}" class="nav-link {{ request()->routeIs('allBlogCategories') ? 'active' : '' }}">
                      <i class="fas fa-tags nav-icon"></i>
                      <p>All Blog Categories</p>
                  </a>
              </li>
          </ul>
      </li>

      <li class="nav-item">
          <a href="{{ route('admin.companyDetail') }}" class="nav-link {{ (request()->is('admin/company-details*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>Company Details</p>
          </a>
      </li>

    </ul>
  </nav>