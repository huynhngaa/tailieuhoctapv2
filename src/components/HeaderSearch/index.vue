<template>
<div class="custom_input">
    <input class="input" type="text" placeholder="Tìm kiếm">
</div>
</template>

<script>
// fuse is a lightweight fuzzy-search module
// make search results more in line with expectations
import { defineComponent } from 'vue';
import Fuse from 'fuse.js';
import path from 'path-browserify';
import store from '@/store';

export default defineComponent({
  name: 'HeaderSearch',
  data() {
    return {
      search: '',
      options: [],
      searchPool: [],
      show: false,
      fuse: undefined
    };
  },
  computed: {
    routes() {
      return store.permission().routes;
    }
  },
  watch: {
    routes() {
      this.searchPool = this.generateRoutes(this.routes);
    },
    searchPool(list) {
      this.initFuse(list);
    },
    show(value) {
      if (value) {
        document.body.addEventListener('click', this.close);
      } else {
        document.body.removeEventListener('click', this.close);
      }
    }
  },
  mounted() {
    this.searchPool = this.generateRoutes(this.routes);
  },
  methods: {
    click() {
      this.show = !this.show;
      if (this.show) {
        this.$refs.headerSearchSelect && this.$refs.headerSearchSelect.focus();
      }
    },
    close() {
      this.$refs.headerSearchSelect && this.$refs.headerSearchSelect.blur();
      this.options = [];
      this.show = false;
    },
    change(val) {
      this.$router.push(val.path);
      this.search = '';
      this.options = [];
      this.$nextTick(() => {
        this.show = false;
      });
    },
    initFuse(list) {
      this.fuse = new Fuse(list, {
        shouldSort: true,
        threshold: 0.4,
        location: 0,
        distance: 100,
        maxPatternLength: 32,
        minMatchCharLength: 1,
        keys: [{
          name: 'title',
          weight: 0.7
        }, {
          name: 'path',
          weight: 0.3
        }]
      });
    },
    // Filter out the routes that can be displayed in the sidebar
    // And generate the internationalized title
    generateRoutes(routes, basePath = '/', prefixTitle = []) {
      let res = [];

      for (const router of routes) {
        // skip hidden router
        if (router.meta && router.meta.hidden) { continue; }

        const data = {
          path: path.resolve(basePath, router.path),
          title: [...prefixTitle]
        };

        if (router.meta && router.meta.title) {
          data.title = [...data.title, router.meta.title];

          if (router.redirect !== 'noRedirect') {
            // only push the routes with title
            // special case: need to exclude parent router without redirect
            res.push(data);
          }
        }

        // recursive child routes
        if (router.children) {
          const tempRoutes = this.generateRoutes(router.children, data.path, data.title);
          if (tempRoutes.length >= 1) {
            res = [...res, ...tempRoutes];
          }
        }
      }
      return res;
    },
    querySearch(query) {
      if (query !== '') {
        this.options = this.fuse.search(query);
      } else {
        this.options = [];
      }
    }
  }
});
</script>

<style lang="scss" scoped>
.custom_input {
	display: flex;
	align-items: center;
	position: relative;
	width: 25rem;
  margin-right: 2rem;
}

.input {
	font-size: 16px;
	padding: 6px 18px;
	width: 25rem;
	padding-right: 17px;
	outline: none;
	background: #FFFFFF;
	color: #000000;
	border: 2px solid #C4D1EB;
	border-radius: 19px;
	transition: .3s ease;
}

.input:focus {
	background: #FFFFFF;
	border: 1px solid #A7C5FF;
	border-radius: 20px;
}

.input::placeholder {
	color: #DDDDDD;
}

.svg_icon {
	position: absolute;
	right: 15px;
	fill: #364BAB;
	width: 16px;
	height: 16px;
}
</style>
