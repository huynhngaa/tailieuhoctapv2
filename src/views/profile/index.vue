<template>
  <div class="app-container">
    <div v-if="user">
      <el-row :gutter="20">
        <el-col :span="6" :xs="24">
          <el-card style="margin-bottom:20px;">
            <template #header>
              <div class="clearfix">
                <span>About me</span>
              </div>
            </template>
            <div class="user-profile">
              <div class="box-center">
                <pan-thumb :image="user.nd_hinh" :height="'100px'" :width="'100px'" :hoverable="false">
                  <div>Hello</div>
                  {{ user.role }}
                </pan-thumb>
              </div>
              <div class="box-center">
                <div class="user-name text-center">{{ user.nd_hoten }}</div>
                <div class="user-role text-center text-muted">{{ user.vt_ten }}</div>
              </div>
            </div>
            <div class="user-bio">
              <div class="user-education user-bio-section">
                <div class="user-bio-section-header">
                  <svg-icon icon-class="education" /><span>Education</span>
                </div>
                <div class="user-bio-section-body">
                  <div class="text-muted">
                    JS in Computer Science from the University of Technology
                  </div>
                </div>
              </div>
              <div class="user-skills user-bio-section">
                <div class="user-bio-section-header">
                  <svg-icon icon-class="skill" /><span>Skills</span>
                </div>
                <div class="user-bio-section-body">
                  <div class="progress-item">
                    <span>Vue</span>
                    <el-progress :percentage="70" />
                  </div>
                  <div class="progress-item">
                    <span>JavaScript</span>
                    <el-progress :percentage="18" />
                  </div>
                  <div class="progress-item">
                    <span>Css</span>
                    <el-progress :percentage="12" />
                  </div>
                  <div class="progress-item">
                    <span>ESLint</span>
                    <el-progress :percentage="100" status="success" />
                  </div>
                </div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="18" :xs="24">
          <el-card>
            <el-tabs v-model="activeTab">
              <el-tab-pane label="Bài viết" name="activity">
                <div class="user-activity">
                  <div v-for="bv in post" :key="bv.bv_ma" class="post">
                    <div class="user-block">
                      <span class="username text-muted">{{ bv.bv_tieude }}</span>
                      <span class="description">{{ bv.bv_ngaydang }}</span>
                    </div>
                    <p v-html="bv.bv_noidung"></p>
                    <ul class="list-inline">
                      <li>
                        <span class="link-black text-sm">
                          <i class="el-icon-share" />
                          Share
                        </span>
                      </li>
                      <li>
                        <span class="link-black text-sm">
                          <svg-icon icon-class="like" />
                          Like
                        </span>
                      </li>
                    </ul>
                  </div>
                </div>
              </el-tab-pane>


              <el-tab-pane label="Hồ sơ" name="account">
                <el-form>
                  <el-form-item label="Name">
                    <el-input v-model.trim="userName" />
                  </el-form-item>
                  <el-form-item label="Email">
                    <el-input v-model.trim="userEmail" />
                  </el-form-item>
                  <el-form-item>
                    <el-button type="primary" @click="submit">Update</el-button>
                  </el-form-item>
                </el-form>
              </el-tab-pane>
            </el-tabs>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { getNguoiDungDetail } from '@/api/nguoidung';
import { ElMessage } from 'element-plus';
import { getListBaiVietOfUser } from '@/api/baiviet';
const activeTab = ref('activity');
const loading = ref(true);

const route = useRoute();
const ndUserName = String(route.query.nd_username);

// Fetch user details on component mount
const user = ref({
  nd_diachi: "",
  nd_email: "",
  nd_gioitinh: 1,
  nd_hinh: "",
  nd_hoten: "",
  nd_matkhau: "",
  nd_ngaysinh: "",
  nd_sdt: "",
  nd_username: '',
  vt_ma: 0,
  vt_ten: ''
});
const post = ref(
  [
    {
      bv_ma: 0,
      bv_ngaydang: "",
      bv_noidung: "",
      bv_tieude: "",
      nd_username: "",
      nd_hoten: "",
    }
  ]

)

onMounted(async () => {
  try {
    const details = await getNguoiDungDetail(ndUserName);
    user.value = details; // Assign fetched details to user
    console.log('User details:', user.value);


    const posts = await getListBaiVietOfUser(ndUserName);
    post.value = posts; // Assign fetched details to user
    console.log(' details:', post.value);
  } catch (err) {
    console.error('Error loading user details:', err);
    ElMessage.error('Failed to load user details.');
  } finally {
    loading.value = false; // Update loading state
  }
});
</script>


<style scoped>
.box-center {
  margin: 0 auto;
  display: table;
}

.text-muted {
  color: #777;
}

.user-profile {
  .user-name {
    font-weight: bold;
  }

  .box-center {
    padding-top: 10px;
  }

  .user-role {
    padding-top: 10px;
    font-weight: 400;
    font-size: 14px;
  }

  .box-social {
    padding-top: 30px;

    .el-table {
      border-top: 1px solid #dfe6ec;
    }
  }

  .user-follow {
    padding-top: 20px;
  }
}

.user-bio {
  margin-top: 20px;
  color: #606266;

  span {
    padding-left: 4px;
  }

  .user-bio-section {
    font-size: 14px;
    padding: 15px 0;

    .user-bio-section-header {
      border-bottom: 1px solid #dfe6ec;
      padding-bottom: 10px;
      margin-bottom: 10px;
      font-weight: bold;
    }
  }
}

.user-activity {
  .user-block {

    .username,
    .description {
      display: block;
      /* margin-left: 50px; */
      padding: 2px 0;
    }

    .username {
      font-size: 16px;
      color: #000;
    }

    :after {
      clear: both;
    }

    .img-circle {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      float: left;
    }

    span {
      font-weight: 500;
      font-size: 12px;
    }
  }

  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;

    .image {
      width: 100%;
      height: 100%;

    }

    .user-images {
      padding-top: 20px;
    }
  }

  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;

    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }

    .link-black {

      &:hover,
      &:focus {
        color: #999;
      }
    }
  }

}

.box-center {
  margin: 0 auto;
  display: table;
}

.text-muted {
  color: #777;
}
</style>
