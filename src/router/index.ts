import { markRaw } from 'vue';
import { createRouter, createWebHistory } from 'vue-router'; 
import type { Router, RouteRecordRaw, RouteComponent } from 'vue-router';
import { Help as IconHelp } from '@element-plus/icons-vue';
import { LuFlower2 } from "react-icons/lu";
/* Layout */
const Layout = ():RouteComponent => import('@/layout/index.vue');

/* Router Modules */
import componentsRouter from './modules/components';
import chartsRouter from './modules/charts';
import nestedRouter from './modules/nested';
import tableRouter from './modules/table';


// export const  routerMain:RouteRecordRaw[] =[ 
//   {
//     path: '/detail/:bv_ma',
//     name: 'Detail',
//     component: () => import('@/views/documentation/detail.vue')
//   }
// ];

export const constantRoutes:RouteRecordRaw[] = [
  {
    path: '/redirect',
    component: Layout,
    meta: { hidden: true },
    children: [
      {
        path: '/redirect/:path(.*)',
        component: () => import('@/views/redirect/index.vue')
      }

    ]
  },
 
  // {
  //   path: '/documentation',
  //   component: Layout,
  //   meta: { hidden: true },
  //   children: [
  //     {
  //       path: 'detail/:bv_ma',
  //          component: () => import('@/views/documentation/detail.vue'),
  //     }
  //   ]
  // },
  // {
  //   path: '/detail;',
  //   component: () => import('@/views/documentation/detail.vue'),
  //   meta: { hidden: true }
  // },
  // {
  //   path: '/detail/:bv_ma',
  //   name: 'Detail',
  //   component: () => import('@/views/documentation/detail.vue'),
  //   meta: { hidden: true }
  // },
  {
    path: '/login',
    component: () => import('@/views/login/index.vue'),
    meta: { hidden: true }
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/auth-redirect.vue'),
    meta: { hidden: true }
  },
  {
    path: '/404',
    component: () => import('@/views/error-page/404.vue'),
    meta: { hidden: true }
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401.vue'),
    meta: { hidden: true }
  },
 
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index.vue'),
        name: 'Dashboard',
        meta: { title: 'Bảng điều khiển',  icon: 'dashboard', affix: true }
      }
    ]
  },
  {
    path: '/danhmuc',
    component: Layout,
    redirect: 'danhmuc/phancong-danhmuc/',
    children: [
      {
        path: 'phancong-danhmuc',
        component: () => import('@/views/danhmuc/phancong-danhmuc.vue'),
        name: 'PhanCongDanhMuc',
        meta: {
          title: 'Phân công quản lý danh mục',
          icon: 'excel',
          roles: ['admin'] 
        }
      }
    ]
  },

  {
    path: '/documentation',
    component: Layout,
    redirect: '/documentation/index',
    meta: { title: 'Quản lý nông dân', icon: 'user', affix: true },
    children: [
      {
        path: 'index',
        component: () => import('@/views/documentation/index.vue'),
        name: 'Documentation',
        meta: { title: 'Quản lý nông dân', icon: 'user', affix: true }
      },
      {
        path: 'detail/:bv_ma',
        name: 'Detail',
        component: () => import('@/views/documentation/detail.vue'),
        meta: { hidden: true } 
      }
    ]
  },
  
  
  {
    path: '/profile',
    component: Layout,
    redirect: '/profile/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/profile/index.vue'),
        name: 'Profile',
        meta: { title: 'Quản lý công cụ', icon: 'user', noCache: true },
        props: route => ({ nd_username: route.query.nd_username })
      }
    ]
  }
];


export const asyncRoutes:RouteRecordRaw[] = [
  {
    path: '/guide',
    component: Layout,
    redirect: '/guide/index',
        name: 'Guide',
        meta: {alwaysShow: true, title: 'Quản lý cây trồng', icon: 'guide', affix: true },
        children: [
          {
            path: 'index',
            component: () => import('@/views/guide/index.vue'),
            // name: 'PagePermission',
            meta: {
              title: 'Hoa',
            
            }
          },
          {
            path: 'directive',
            component: () => import('@/views/permission/directive.vue'),
            name: 'DirectivePermission',
            meta: {
              title: 'Rau củ'
             
            }
          }]
      
    
  },
  {
    path: '/baiviet',
    component: Layout,
    redirect: '/baiviet/index',
    name: 'QuanLyBaiViet',
    meta: {
      title: 'Quản lý bài viết',
      icon: markRaw(IconHelp)
    },
    children: [
      {
        path: 'index',
        component: () => import('@/views/baiviet/index.vue'),
        name: 'DanhSachBaiViet',
        meta: { title: 'Danh sách bài viết', icon: 'edit' }
      },
      {
        path: 'kiem-duyet',
        component: () => import('@/views/baiviet/kiem-duyet.vue'),
        name: 'KiemDuyetBaiViet',
        meta: { title: 'Kiểm duyệt bài viết', icon: 'edit' }
      },
   
      {
       
        path: 'chitiet-baiviet/:bv_ma',
        component: () => import('@/views/baiviet/chitiet-baiviet.vue'),
        name: 'ChiTietBaiVietQuanLy',
        meta: { hidden: true } 
      }
    ]
  
  },
  {
    path: '/nguoidung',
    component: Layout,
    redirect: '/nguoidung/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/nguoidung/index.vue'),
        name: 'QuanLyNguoiDung',
        meta: {
          title: 'Quản lý người dùng',
          icon: 'user',
          roles: ['admin'] 
        }
      }
    ]
  },
  {
    path: '/danhmuc',
    component: Layout,
    redirect: '/danhmuc/chitietdanhmuc',
    children: [
      {
        path: 'chitietdanhmuc',
        component: () => import('@/views/danhmuc/index.vue'),
        name: 'ChiTietDanhMuc',
        meta: {
          title: 'Quản lý danh mục',
          icon: 'excel',
          roles: ['admin'] 
        }
      }
    ]
  },
  {
    path: '/monhoc',  
    component: Layout,
    redirect: '/monhoc/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/monhoc/index.vue'),
        name: 'MonHoc',
        meta: {
          title: 'Quản lý môn học',
          icon: 'excel',
          roles: ['admin'] 
        }
      }
    ]
  },
  {
    path: '/khoilop',
    component: Layout,
    redirect: '/khoilop/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/khoilop/index.vue'),
        name: 'KhoiLop',
        meta: {
          title: 'Quản lý khối lớp',
          icon: 'excel',
          roles: ['admin'] 
        }
      }
    ]
  },

  // /** when your routing map is too long, you can split it into small modules **/
  componentsRouter,
  // chartsRouter,
  // nestedRouter,
  // tableRouter,

  {
    path: '/example',
    component: Layout,
    redirect: '/example/list',
    name: 'Example',
    meta: {
      title: 'Quản lý lớp học',
      icon: markRaw(IconHelp)
    },
    children: [
      {
        path: 'create',
        component: () => import('@/views/example/create.vue'),
        name: 'CreateArticle',
        meta: { title: 'Thêm lớp mới', icon: 'edit' }
      },
   
      {
        path: 'list',
        component: () => import('@/views/example/list.vue'),
        name: 'ArticleList',
        meta: { title: 'Sửa thông tin', icon: 'list' }
      }
    ]
  },


  {
    path: '/error',
    component: Layout,
    redirect: 'noRedirect',
    name: 'ErrorPages',
    meta: {
      title: 'Thống kê',
      icon: '404'
    },
    children: [
      {
        path: '401',
        component: () => import('@/views/error-page/401.vue'),
        name: 'Page401',
        meta: { title: 'Thống kê lớp', noCache: true }
      },
      {
        path: '404',
        component: () => import('@/views/error-page/404.vue'),
        name: 'Page404',
        meta: { title: 'Thống kê tài sản', noCache: true }
      }
    ]
  },

 


  // 404 page must be placed at the end !!!
  { path: '/:pathMatch(.*)*', redirect: '/404', meta: { hidden: true }}
];

console.log('BASE_URL=', import.meta.env);

const createTheRouter = ():Router => createRouter({
  // history: createWebHashHistory(import.meta.env.BASE_URL),
  // 注意，如果要配置 HTML5 模式，则需要修改nginx配置，参考资料：
  // https://router.vuejs.org/zh/guide/essentials/history-mode.html
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior: () => ({ top: 0 }),
  routes: constantRoutes
});

interface RouterPro extends Router {
  matcher: unknown;
}

const router = createTheRouter() as RouterPro;

export function resetRouter() {
  const newRouter = createTheRouter() as RouterPro;
  router.matcher = newRouter.matcher; // reset router
}

export default router;
