<template>
    <el-row>
        <el-col :xs="24" :sm="18" :md="18" :lg="18" :xl="18">
            <div class="app-container">
                <p v-if="baiVietDetail">
                    <router-link :to="{ path: '/profile/index', query: { nd_username: baiVietDetail.nd_username } }">
                        <span class="name">{{ baiVietDetail.nd_hoten }} </span>
                    </router-link>
                    đã đăng lúc {{ baiVietDetail.bv_ngaydang }}
                </p>
                <p class="title">{{ baiVietDetail?.bv_tieude }}</p>
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
        <el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="6">
            <div class="related">
                <p class="title-related">Bài viết liên quan</p>
                <p v-for="post in relatedPosts" :key="post.bv_ma">
                    <router-link :to="{ name: 'Detail', params: { bv_ma: post.bv_ma } }">
                        ➣ {{ post.bv_tieude }}
                    </router-link>
                </p>
            </div>
        </el-col>
    </el-row>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { getBaiVietDetail, getRelatedPosts } from '@/api/baiviet';
import { getListBinhLuan } from '@/api/binhluan';
interface BaiVietDetail {
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
interface relatedPosts {
    bv_ma: number;
    bv_tieude: string;


}
interface Comment {
    bl_ma: number;
    nd_hoten: string;
    bl_noidung: string;
    current_time: string;
    nd_hinh: string;
    nd_username: string;
    replies?: Comment[]; // Nested comments (if any)
}
const route = useRoute();
const bvMa = Number(route.params.bv_ma);
const dmMa = ref<number | null>(null);
const baiVietDetail = ref<BaiVietDetail | null>(null);
const relatedPosts = ref<relatedPosts[]>([]);
const comments = ref<Comment[]>([]);

const loading = ref(true);
const error = ref<string | null>(null);
const ratePoint = ref(0);
const commentInput = ref('')
onMounted(async () => {
    try {
        baiVietDetail.value = await getBaiVietDetail(bvMa);
        dmMa.value = baiVietDetail?.value?.dm_ma ?? null;

        if (dmMa.value !== null) {
            relatedPosts.value = await getRelatedPosts(bvMa, dmMa.value);

        } else {
            console.error('dmMa is null');
        }
        comments.value = await getListBinhLuan(bvMa);
        ratePoint.value = baiVietDetail.value?.bv_diemtrungbinh ?? 0;
        console.log('comments:', comments.value);
    } catch (err) {
        console.error('Lỗi khi tải dữ liệu bài viết:', err);
        error.value = 'Lỗi khi tải dữ liệu bài viết';
    } finally {
        loading.value = false;
    }
});


function submitComment() {

}
const goToHomePage = () => {
    window.location.href = '/';
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