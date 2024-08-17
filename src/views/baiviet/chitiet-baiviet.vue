<template>
    <el-row>
      <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
        <div class="app-container">
          <p v-if="baiVietDetail" style="margin: 0;">
            <div style="margin-bottom: 2rem;">
              <el-button size="small" type="primary" @click="handleHuYDuyet">Huỷ duyệt</el-button>
              <el-button size="small" type="success" @click="handleDuyetBaiViet">Duyệt bài viết</el-button>
              <el-button size="small" type="info" @click="handleAn">Ẩn</el-button>
              <el-button size="small" type="warning" @click="handleHien">Hiện</el-button>
              <el-button size="small" type="danger" @click="handleXoa">Xoá</el-button>
            </div>
  
            <span style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
              <span>
                <router-link :to="{ path: '/profile/index', query: { nd_username: baiVietDetail.nd_username } }">
                  <span class="name" style="font-weight: bold; margin-right: 10px;">
                    {{ baiVietDetail.nd_hoten }}
                  </span>
                </router-link>
                đã đăng lúc {{ baiVietDetail.bv_ngaydang }}
              </span>
            </span>
          </p>
  
          <p class="title">{{ baiVietDetail?.bv_tieude }}</p>
          <div style="display: flex; justify-content: center; align-items: center;">
            <el-tag style="font-size: 1rem;" size="large" :key="baiVietDetail?.tt_ma" :type="getTagType(baiVietDetail?.tt_ma)" effect="light">
              {{ baiVietDetail?.tt_ten ? baiVietDetail?.tt_ten : 'Chờ duyệt' }}
            </el-tag>
          </div>
          <p v-html="baiVietDetail?.bv_noidung"></p>
          <el-divider />
  
          <div class="action-row">
            <div class="action-item action-left">
              <el-icon class="action-icon">
                <ChatRound />
              </el-icon>
              <span class="action-text">{{ baiVietDetail?.slbl }}</span>
            </div>
            <el-rate v-model="ratePoint" size="large" class="action-rate" />
            <div class="action-item action-right">
              <el-icon class="action-icon">
                <Flag />
              </el-icon>
              <span class="action-text">Báo cáo</span>
            </div>
          </div>
  
          <el-input v-model="commentInput" :rows="3" type="textarea" placeholder="Please input" />
          <div class="comment-submit">
            <el-button type="primary" @click="submitComment">Gửi bình luận</el-button>
          </div>
          <p>Bình luận: </p>
          <div v-for="item in comments" :key="item.bl_ma" class="comment-container">
            <el-row>
              <el-col :xs="8" :sm="2" :md="2" :lg="2" :xl="2">
                <div>
                  <el-avatar v-if="item.nd_hinh" :src="`/public/img/${item.nd_hinh}`" />
                  <el-avatar v-else>{{ item.nd_username }}</el-avatar>
                </div>
              </el-col>
              <el-col :xs="16" :sm="22" :md="22" :lg="22" :xl="22">
                <div class="list-comment">
                  <p class="text-item"><b>{{ item.nd_hoten }}</b></p>
                  <p class="text-item">{{ item.bl_noidung }}</p>
                </div>
              </el-col>
            </el-row>
  
            <!-- Hiển thị bình luận con -->
            <div v-if="item.replies && item.replies.length" class="replies-container">
              <div v-for="reply in item.replies" :key="reply.bl_ma" class="reply">
                <el-row>
                  <el-col :xs="8" :sm="2" :md="2" :lg="2" :xl="2">
                    <div>
                      <el-avatar v-if="reply.nd_hinh" :src="`/public/img/${reply.nd_hinh}`" />
                      <el-avatar v-else>{{ reply.nd_username }}</el-avatar>
                    </div>
                  </el-col>
                  <el-col :xs="16" :sm="22" :md="22" :lg="22" :xl="22">
                    <div class="list-comment">
                      <p class="text-item"><b>{{ reply.nd_hoten }}</b></p>
                      <p class="text-item">{{ reply.bl_noidung }}</p>
                    </div>
                  </el-col>
                </el-row>
              </div>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>
  </template>
  
  <script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { getBaiVietDetailQuanLy, getRelatedPosts } from '@/api/baiviet';
import { getListBinhLuan } from '@/api/binhluan';
import { getListTrangThai } from '@/api/trangthai';
import { suaTrangThaiBaiViet } from '@/api/baiviet'; // Import your API function
import { ElMessage, ElMessageBox } from 'element-plus';
import axios from 'axios';

interface BaiVietDetail {
  tt_ma: number;
  tt_ten: number;
  bv_ma: number;
  dm_ma: number;
  nd_username: string;
  bv_tieude: string;
  bv_noidung: string;
  bv_ngaydang: string;
  bv_luotxem: number;
  bv_diemtrungbinh: number;
  vt_ma: number;
  nd_hoten: string;
  nd_gioitinh: number;
  nd_email: string;
  nd_sdt: string;
  nd_matkhau: string;
  nd_diachi: string;
  nd_ngaysinh: string;
  nd_hinh: string;
  nd_ngaytao: string;
  dm_ten: string;
  mh_ten: string;
  kl_ten: string;
  currentTimestamp: string;
  slbl: number;
}

interface trangThai {
  tt_ma: number;
  tt_ten: string;
}

interface Comment {
  bl_ma: number;
  nd_hoten: string;
  bl_noidung: string;
  current_time: string;
  nd_hinh: string;
  nd_username: string;
  replies?: Comment[];
}

const route = useRoute();
const bvMa = Number(route.params.bv_ma);
const dmMa = ref<number | null>(null);
const baiVietDetail = ref<BaiVietDetail | null>(null);
const trangThai = ref<trangThai[]>([]);
const comments = ref<Comment[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const ratePoint = ref(0);
const commentInput = ref('');

// Function to reload data
const reloadData = async () => {
  try {
    loading.value = true;
    baiVietDetail.value = await getBaiVietDetailQuanLy(bvMa);
    dmMa.value = baiVietDetail?.value?.dm_ma ?? null;
    trangThai.value = await getListTrangThai();
    comments.value = await getListBinhLuan(bvMa);
    ratePoint.value = baiVietDetail.value?.bv_diemtrungbinh ?? 0;
  } catch (err) {
    console.error('Lỗi khi tải dữ liệu bài viết:', err);
    error.value = 'Lỗi khi tải dữ liệu bài viết';
  } finally {
    loading.value = false;
  }
};

// Fetch data on component mount
onMounted(() => {
  reloadData();
});

// Function to get the tag type
const getTagType = (trangThai) => {
  switch (trangThai) {
    case 1:
      return 'success';
    case 2:
      return 'info';
    case 5:
      return 'primary';
    case 4:
      return 'danger';
    default:
      return 'warning';
  }
};

// Function to handle the "Huỷ duyệt" button click
const handleHuYDuyet = async () => {
  try {
    await ElMessageBox.confirm('Bạn chắc chắn muốn huỷ bài viết?', 'Xác nhận huỷ bài viết', {
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy',
      type: 'warning',
    }).then(async () => {
      await suaTrangThaiBaiViet(bvMa, 2); // Status code for canceling
      ElMessage.success('Đã huỷ bài viết.');
      await reloadData(); // Reload data after action
    }).catch(() => {
      ElMessage.info('Hành động đã bị hủy.');
    });
  } catch (error) {
    console.error('Lỗi khi huỷ bài viết:', error);
    ElMessage.error('Lỗi khi huỷ bài viết.');
  }
};

// Function to handle the "Duyệt bài viết" button click
const handleDuyetBaiViet = async () => {
  try {
    await ElMessageBox.confirm('Bạn chắc chắn muốn duyệt bài viết?', 'Xác nhận duyệt bài viết', {
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy',
      type: 'warning',
    }).then(async () => {
      await suaTrangThaiBaiViet(bvMa, 1); // Status code for approving
      ElMessage.success('Đã duyệt bài viết.');
      await reloadData(); // Reload data after action
    }).catch(() => {
      ElMessage.info('Hành động đã bị hủy.');
    });
  } catch (error) {
    console.error('Lỗi khi duyệt bài viết:', error);
    ElMessage.error('Lỗi khi duyệt bài viết.');
  }
};

// Function to handle the "Ẩn" button click
const handleAn = async () => {
  try {
    await ElMessageBox.confirm('Bạn chắc chắn muốn ẩn bài viết?', 'Xác nhận ẩn bài viết', {
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy',
      type: 'warning',
    }).then(async () => {
      await suaTrangThaiBaiViet(bvMa, 5); // Status code for hiding
      ElMessage.success('Đã ẩn bài viết.');
      await reloadData(); // Reload data after action
    }).catch(() => {
      ElMessage.info('Hành động đã bị hủy.');
    });
  } catch (error) {
    console.error('Lỗi khi ẩn bài viết:', error);
    ElMessage.error('Lỗi khi ẩn bài viết.');
  }
};

// Function to handle the "Hiện" button click
const handleHien = async () => {
  try {
    await ElMessageBox.confirm('Bạn chắc chắn muốn hiện bài viết?', 'Xác nhận hiện bài viết', {
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy',
      type: 'warning',
    }).then(async () => {
      await suaTrangThaiBaiViet(bvMa, 1); // Status code for showing
      ElMessage.success('Đã hiện bài viết.');
      await reloadData(); // Reload data after action
    }).catch(() => {
      ElMessage.info('Hành động đã bị hủy.');
    });
  } catch (error) {
    console.error('Lỗi khi hiện bài viết:', error);
    ElMessage.error('Lỗi khi hiện bài viết.');
  }
};

// Function to handle the "Xoá" button click
const handleXoa = async () => {
  try {
    await ElMessageBox.confirm('Bạn chắc chắn muốn xoá bài viết?', 'Xác nhận xoá bài viết', {
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy',
      type: 'warning',
    }).then(async () => {
      await suaTrangThaiBaiViet(bvMa, 4); // Status code for deleting
      ElMessage.success('Đã xoá bài viết.');
      await reloadData(); // Reload data after action
    }).catch(() => {
      ElMessage.info('Hành động đã bị hủy.');
    });
  } catch (error) {
    console.error('Lỗi khi xoá bài viết:', error);
    ElMessage.error('Lỗi khi xoá bài viết.');
  }
};

// Function to handle comment submission
const submitComment = async () => {
  try {
    await axios.post('/api/comments', {
      bv_ma: bvMa,
      nd_username: 'currentUsername', // Replace with actual username
      bl_noidung: commentInput.value,
      // other necessary data
    });
    commentInput.value = '';
    await reloadData(); // Reload comments after submission
  } catch (error) {
    console.error('Lỗi khi gửi bình luận:', error);
    ElMessage.error('Lỗi khi gửi bình luận.');
  }
};
</script>

  

<style scoped>
.title {
    text-align: center;
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 10px;
}

.action-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.action-item {
    display: flex;
    align-items: center;
}

.action-left {
    margin-right: auto;
}

.action-right {
    margin-left: auto;
}

.action-icon {
    margin-right: 8px;
}

.action-text {
    font-size: 1rem;
}

.action-rate {
    margin: 0 20px;
}

.back-button-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.related {
    margin-top: 30%;
    margin-right: 10%;
    margin-left: 10%;

}

.comment-submit {
    text-align: right;
    margin-top: 5px;
}

.el-divider--horizontal {
    margin: 5px !important;
}

.title-related {
    font-size: 1.1rem;
    font-weight: 600;
}

.comment-container {
    margin-bottom: 16px;
    padding: 10px 0 16px 0px;
    border-radius: 8px;
}

.replies-container {
    margin-top: 15px;
    margin-left: 55px;
    /* Indent child comments */
}

.reply {
    margin-bottom: 8px;
    /* Space between replies */
}

.list-comment {
    padding: 8px 0 8px 8px;
    background-color: rgb(231, 231, 231);
    border-radius: 8px;
}

.text-item {
    margin: 0;
}

.name {
    color: blue;
    font-weight: 900;
}
</style>