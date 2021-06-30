import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/pages/Home'
import Auth from '../views/pages/Auth'
import User from '../views/pages/User'

const routes = [
  {
    path: '/',
    name: 'Home',
    meta: {layout: 'main'},
    component: Home
  },
  {
    path: '/user/:usr_id',
    name: 'UserID',
    meta: {layout: 'main'},
    component: User,
  },
  {
    path: '/auth/:socnet',
    name: 'Auth',
    component: Auth,
  },
  {
    path: '/test/:id/editor',
    name: 'TestEditor',
    meta: {layout: 'main'},
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/pages/TestEditor.vue')
  },
  {
    path: '/test/:id',
    name: 'TestBasic',
    meta: {layout: 'main'},
    component: () => import(/* webpackChunkName: "about" */ '../views/pages/TestBasic.vue')
  },
  {
    path: '/gtest/:gid',
    name: 'GroupTestBasic',
    meta: {layout: 'main'},
    component: () => import(/* webpackChunkName: "about" */ '../views/pages/TestBasic.vue')
  },
  {
    path: '/result/:id',
    name: 'TestResult',
    meta: {layout: 'main'},
    component: () => import('../views/pages/TestResult.vue')
  },
  {
    path: '/mytests',
    name: 'MyTests',
    meta: {layout: 'main'},
    component: () => import('../views/pages/MyTests.vue')
  },
  {
    path: '/myresults',
    name: 'MyResults',
    meta: {layout: 'main'},
    component: () => import('../views/pages/MyResults.vue')
  },
  {
    path: '/groups',
    name: 'Groups',
    meta: {layout: 'main'},
    component: () => import('../views/pages/Groups.vue')
  },
  {
    path: '/group/:id',
    name: 'Group',
    meta: {layout: 'main'},
    component: () => import('../views/pages/Group.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
