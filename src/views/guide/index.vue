<template>
  <div class="app-container">
    <el-button @click="openEditModal" type="primary" :icon="Plus">Thêm mới</el-button>
    <el-table v-loading="loading" :data="paginatedTableData" style="width: 100%">
      <el-table-column label="ID" prop="id" />
      <el-table-column label="Image">
        <template #default="scope">
          <img :src="scope.row.img" alt="Image" style="width: 50px; height: 50px;" />
        </template>
      </el-table-column>
      
      
      <el-table-column label="Name" prop="name" />

      <el-table-column label="Thời gian thu hoạch" prop="time" />
      <el-table-column align="right">
        <template #header>
          <el-input v-model="search" size="small" placeholder="Type to search" />
        </template>
        <template #default="scope">
          <el-button type="success" size="small" @click="openDetailModal(scope.row)">
            <el-icon>
              <View />
            </el-icon>
          </el-button>
          <el-button type="warning" size="small" @click="openEditModal(scope.row)">
            <el-icon>
              <Edit />
            </el-icon>
          </el-button>
          <el-button size="small" type="danger" @click="openDeleteModal(scope.row)">
            <el-icon>
              <Delete />
            </el-icon>
          </el-button>
          

        </template>
      </el-table-column>
    </el-table>
    <el-pagination class="pagination" v-model:current-page="currentPage" v-model:page-size="pageSize"
      :page-sizes="[5, 10, 50, 100]" :disabled="disabled" :background="background"
      layout="total, sizes, prev, pager, next" :total="totalItems" @size-change="handleSizeChange"
      @current-change="handleCurrentChange" />

    <!-- Modal Chi tiết -->
    <el-dialog v-model="dialogDetail" title="Chi tiết hoa" width="800">
      <el-descriptions :column="2" border>

        <el-descriptions-item label="ID">{{ selectedRow?.id }}</el-descriptions-item>

        <el-descriptions-item label="Image">
          <img :src="selectedRow?.img" alt="Image" style="width: 100px; height: 100px;" />
        </el-descriptions-item>
        <el-descriptions-item label="Tên">{{ selectedRow?.name }}</el-descriptions-item>
       <el-descriptions-item
         label="Thời gian thu hoạch">{{  dayjs(selectedRow?.time).format('MM-DD-YYYY')
         }}</el-descriptions-item>
         </el-descriptions>
    </el-dialog>
    <!-- Modal thêm và Sửa -->

    <el-dialog v-model="dialogFormVisible" title="Thông tin hoa" width="500">
      <el-form :model="selectedRow" v-if="selectedRow">
        <el-form-item v-if="selectedRow.id" label="ID" :label-width="formLabelWidth">
          <el-input disabled v-model="selectedRow.id" />
        </el-form-item>

        <el-form-item label="Hình ảnh" :label-width="formLabelWidth">
          <el-upload action="#" list-type="picture-card" :auto-upload="false"
            :file-list="selectedRow.img ? [{ url: selectedRow.img }] : []">
            <el-icon>
              <Plus />
            </el-icon>
            <template #file="{ file }">
              <div>
                <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                <span class="el-upload-list__item-actions">
                  <span class="el-upload-list__item-preview" @click="handlePictureCardPreview(file)">
                    <el-icon><zoom-in /></el-icon>
                  </span>
                  <span v-if="!disabled" class="el-upload-list__item-delete" @click="handleRemove(file)">
                    <el-icon>
                      <Delete />
                    </el-icon>
                  </span>
                </span>
              </div>
            </template>
          </el-upload>
        </el-form-item>
        <el-form-item label="Tên" :label-width="formLabelWidth">
          <el-input v-model="selectedRow.name" autocomplete="off" />
        </el-form-item>
        <el-form-item label="Thời gian thu hoạch" :label-width="formLabelWidth">
          <el-date-picker v-model="selectedRow.time" type="date" placeholder="Pick a day" />
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="dialogFormVisible = false">Huỷ</el-button>

          <el-button v-if="!selectedRow?.id" @click="handleSubmit" type="primary">Thêm</el-button>
          <el-button v-else type="primary" @click="open">Sửa</el-button>

        </div>
      </template>
    </el-dialog>
    <!-- Modal Xoá -->
    <el-dialog v-model="centerDialogVisible" title="Xác nhận" width="500" align-center>
      <span>Bạn có muốn xoá hoa này không?</span>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="centerDialogVisible = false">Huỷ</el-button>
          <el-button type="danger" plain @click="confirmDelete">Xoá</el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, onMounted, onBeforeMount } from 'vue'
import { getFlower } from '../../api/plant'
import type { UploadFile } from 'element-plus'
import { Delete, Edit, Plus } from '@element-plus/icons-vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import dayjs from 'dayjs'

interface User {
  id: string
  name: string
  img: string
  time: Date
}

const dialogFormVisible = ref(false)
const dialogDetail = ref(false)
const centerDialogVisible = ref(false)
const formLabelWidth = '140px'
const search = ref('')
const tableData = ref<User[]>([])
const selectedRow = ref<User | null>(null)
const currentPage = ref(1)
const pageSize = ref(5)
const background = ref(false)
const disabled = ref(false)
const dialogImageUrl = ref('')
const dialogVisible = ref(false)
const loading = ref(false)


onBeforeMount(async () => {
  loading.value = true
  try {
    const data = await getFlower()
    tableData.value = data
    loading.value = false
  } catch (error) {
    console.error('lỗi khi lấy api:', error)
  } finally {
    loading.value = false
  }
})

const filterTableData = computed(() =>
  tableData.value.filter(
    (data) =>
      !search.value ||
      data.name.toLowerCase().includes(search.value.toLowerCase())
  )
)
const handleRemove = (file: UploadFile) => {
  console.log(file)
}

const handlePictureCardPreview = (file: UploadFile) => {
  dialogImageUrl.value = file.url!
  dialogVisible.value = true
}
const paginatedTableData = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  return filterTableData.value.slice(start, end)
})

const totalItems = computed(() => filterTableData.value.length)

const handleSizeChange = (val: number) => {
  pageSize.value = val
}
const handleCurrentChange = (val: number) => {
  currentPage.value = val
}

const handleSubmit = () => {
  const id = selectedRow?.value?.id;
  if (!id) {
    const data = selectedRow?.value;
    if (data) {
      const day = dayjs(data.time).format('Y-m-d');
       
      tableData.value.unshift(data);
      dialogFormVisible.value = false;
      selectedRow.value = null;
    }
  } else {
    const index = tableData.value.findIndex((row) => row.id === id);
    if (index > -1) {
      console.log('Sửa:', selectedRow.value);
      tableData.value.splice(index, 1, selectedRow.value!);
      dialogFormVisible.value = false;
      selectedRow.value = null;
    } else {
      console.error('Không tìm thấy dữ liệu để sửa:', id);
    }

  }
}

const open = () => {
  dialogFormVisible.value = false;
  ElMessageBox.confirm(
    'Xác nhận chỉnh sửa',
    'Warning',
    {
      confirmButtonText: 'OK',
      cancelButtonText: 'Cancel',
      type: 'warning',
      center: true,
    }
  )
    .then(() => {
      handleSubmit()
      ElMessage({
        type: 'success',
        message: 'Delete completed',
      })
    })
    .catch(() => {
      ElMessage({
        type: 'info',
        message: 'Delete canceled',
      })
    })
}
const openDetailModal = (row: User) => {
  selectedRow.value = { ...row }
  dialogDetail.value = true
}

const openEditModal = (row: User) => {
  selectedRow.value = { ...row }
  dialogFormVisible.value = true
}

const openDeleteModal = (row: User) => {
  selectedRow.value = { ...row }
  centerDialogVisible.value = true
}

const confirmDelete = () => {
  console.log('Deleting row:', selectedRow.value?.id);
  centerDialogVisible.value = false

};
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

.pagination {
  justify-content: center;
  align-items: center;
  position: fixed;
  bottom: 3rem;
  right: 0rem;
  width: 100%;
}
</style>
