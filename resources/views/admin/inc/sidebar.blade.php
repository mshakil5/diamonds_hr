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

      <li class="nav-item">
        <a href="{{route('alladmin')}}" class="nav-link {{ (request()->is('admin/new-admin*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Admin
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('branches.index')}}" class="nav-link {{ (request()->is('admin/branches*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Branch
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('employees.index')}}" class="nav-link {{ (request()->is('admin/employees*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Employees
          </p>
        </a>
      </li>

      <li class="nav-item d-none">
        <a href="{{route('allUsers')}}" class="nav-link {{ (request()->is('admin/users*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Users
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('holidays.index')}}" class="nav-link {{ (request()->is('admin/holidays*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Holiday
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('attendance.index')}}" class="nav-link {{ (request()->is('admin/attendance*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Attendance
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('products.index')}}" class="nav-link {{ (request()->is('admin/products*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Product
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('stocks.index')}}" class="nav-link {{ (request()->is('admin/stocks*')) ? 'active' : '' }}">
         <i class="fas fa-users"></i>
          <p>
            Stock
          </p>
        </a>
      </li>

      

      <li class="nav-item dropdown {{ request()->is('admin/report*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/blogs*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-blog"></i>
              <p>
                  Report <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a href="{{ route('employeeReport') }}" class="nav-link {{ request()->routeIs('employeeReport') ? 'active' : '' }}">
                      <i class="fas fa-list nav-icon"></i>
                      <p>Employee Report</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('holidayReport') }}" class="nav-link {{ request()->routeIs('holidayReport') ? 'active' : '' }}">
                      <i class="fas fa-tags nav-icon"></i>
                      <p>Holiday Report</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('stockReport') }}" class="nav-link {{ request()->routeIs('stockReport') ? 'active' : '' }}">
                      <i class="fas fa-list nav-icon"></i>
                      <p>Product Stock Report</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('allBlogCategories') }}" class="nav-link {{ request()->routeIs('allBlogCategories') ? 'active' : '' }}">
                      <i class="fas fa-tags nav-icon"></i>
                      <p>Staff Based Stock Report</p>
                  </a>
              </li>
          </ul>
      </li>

      <li class="nav-item dropdown {{ request()->is('admin/settings*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/settings*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-blog"></i>
              <p>
                  Settings <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a href="{{ route('changeBranch') }}" class="nav-link {{ request()->routeIs('changeBranch') ? 'active' : '' }}">
                      <i class="fas fa-list nav-icon"></i>
                      <p>Change Branch</p>
                  </a>
              </li>
          </ul>
      </li>

      <li class="nav-item dropdown d-none {{ request()->is('admin/blogs*') || request()->is('admin/blog-categories*') ? 'menu-open' : '' }}">
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

      <li class="nav-item d-none">
          <a href="{{ route('admin.companyDetail') }}" class="nav-link {{ (request()->is('admin/company-details*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>Company Details</p>
          </a>
      </li>

      {{-- <li class="nav-item">
          <a href="{{ route('admin.role') }}" class="nav-link {{ (request()->is('admin/role*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-shield-alt"></i>
              <p>Roles & Permissions</p>
          </a>
      </li> --}}

    </ul>
  </nav>