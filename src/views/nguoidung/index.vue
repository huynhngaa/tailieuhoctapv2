<template>
    <div class="app-container">
      <!-- Search and Filter Controls -->
      <div class="filter-controls">
        <el-input v-model="search" placeholder="Tìm kiếm" style="width: 200px; margin-right: 20px;" />
  
        <el-select v-model="selectedGender" placeholder="Chọn giới tính" style="width: 200px; margin-right: 20px;">
          <el-option label="Tất cả" :value="null" />
          <el-option label="Nam" :value="2" />
          <el-option label="Nữ" :value="1" />
          <el-option label="Không rõ" :value="0" />
        </el-select>
  
        <el-select v-model="selectedRole" placeholder="Chọn vai trò" style="width: 200px; margin-right: 20px;">
          <el-option label="Tất cả" :value="null" />
          <el-option v-for="role in vaiTroData" :key="role.vt_ma" :label="role.vt_ten" :value="role.vt_ma" />
        </el-select>
        <el-button @click="dialogFormVisible = true" type="primary" :icon="Plus">Thêm người dùng</el-button>
        <el-button  :disabled="isTableEmpty" style="background-color: #35ba9b;" @click="exportToExcel" type="primary"
        :icon="Download">Xuất excel</el-button>
  
        
      </div>
  
      <!-- Table -->
      <el-table :data="paginatedTableData" style="width: 100%; height: 70vh;">
        <el-table-column label="STT" prop="nd_username" width="150" />
        <el-table-column label="Hình">
          <template v-slot="scope">
            <el-avatar v-if="scope.row.nd_hinh !== ''" :src="`/public/img/${scope.row.nd_hinh}`" />
            <el-avatar v-else :icon="UserFilled" />
          </template>
        </el-table-column>
        <el-table-column label="Họ Tên" prop="nd_hoten" width="210" />
        <el-table-column label="Username" prop="nd_username" width="150" />
        <el-table-column label="Giới tính" prop="nd_gioitinh" width="150" :formatter="formatGender" />
        <el-table-column label="Ngày sinh" prop="nd_ngaysinh" width="150" />
        <el-table-column label="Vai trò" prop="vt_ten" width="150">
          <template #default="scope">
            <el-tag :key="scope.row.vt_ten" :type="getTagType(scope.row.vt_ten)" effect="light" round>
              {{ scope.row.vt_ten }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Ngày tạo" prop="nd_ngaytao" width="180" />
        <el-table-column fixed="right" align="right" min-width="180">
          <template #default="scope">
            <el-button type="warning" plain size="small" @click="openEditDialog(scope.row)">
              Chỉnh sửa
            </el-button>
            <el-button size="small" type="danger" @click="handleDelete(scope.$index, scope.row)">
              Xoá
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
      layout="total, sizes, prev, pager, next, jumper"
      background
      :current-page="currentPage"
     :page-sizes="[5, 10, 20, 50, 100]"
      :total="filteredTableData.length"
      @current-change="handlePageChange"
      @size-change="handlePageSizeChange">
    </el-pagination>
      <!-- Form Thêm -->
      <el-dialog v-model="dialogFormVisible" title="Thêm người dùng" width="500">
        <el-form :model="form">
          <el-form-item label="Username: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_username" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Họ tên: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_hoten" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Giới tính: " :label-width="formLabelWidth">
            <el-select v-model="form.nd_gioitinh" placeholder="Chọn giới tính">
              <el-option label="Nam" :value="2" />
              <el-option label="Nữ" :value="1" />
              <el-option label="Không rõ" :value="0" />
            </el-select>
          </el-form-item>
          <el-form-item label="Email: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_email" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Số điện thoại: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_sdt" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Mật khẩu: " :label-width="formLabelWidth">
            <el-input type="password" v-model="form.nd_matkhau" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Địa chỉ: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_diachi" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Ngày sinh: " :label-width="formLabelWidth">
            <el-date-picker v-model="form.nd_ngaysinh" type="date" placeholder="Chọn ngày sinh" />
          </el-form-item>
          <el-form-item label="Hình: " :label-width="formLabelWidth">
            <el-upload action="#" list-type="picture-card" :auto-upload="false">
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
                    <span v-if="!disabled" class="el-upload-list__item-delete" @click="handleDownload(file)">
                      <el-icon>
                        <Download />
                      </el-icon>
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
          <el-form-item label="Vai trò: " :label-width="formLabelWidth">
            <el-select v-model="form.vt_ma" placeholder="Chọn vai trò">
              <el-option v-for="role in vaiTroData" :key="role.vt_ma" :label="role.vt_ten" :value="role.vt_ma" />
            </el-select>
          </el-form-item>
        </el-form>
        <template #footer>
          <div class="dialog-footer">
            <el-button @click="dialogFormVisible = false">Cancel</el-button>
            <el-button type="primary" @click="handleAdd">Confirm</el-button>
          </div>
        </template>
      </el-dialog>
  
      <!-- Form Sửa -->
      <el-dialog v-model="dialogFormEdit" title="Cập nhật người dùng" width="500">
        <el-form :model="form">
          <el-form-item label="Username: " :label-width="formLabelWidth">
            <el-input disabled v-model="form.nd_username" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Họ tên: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_hoten" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Giới tính: " :label-width="formLabelWidth">
            <el-select v-model="form.nd_gioitinh" placeholder="Chọn giới tính">
              <el-option label="Nam" :value="2" />
              <el-option label="Nữ" :value="1" />
              <el-option label="Không rõ" :value="0" />
            </el-select>
          </el-form-item>
          <el-form-item label="Email: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_email" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Số điện thoại: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_sdt" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Địa chỉ: " :label-width="formLabelWidth">
            <el-input v-model="form.nd_diachi" autocomplete="off" />
          </el-form-item>
          <el-form-item label="Ngày sinh: " :label-width="formLabelWidth">
            <el-date-picker v-model="form.nd_ngaysinh" type="date" placeholder="Chọn ngày sinh" />
          </el-form-item>
          <el-form-item label="Hình: " :label-width="formLabelWidth">
            <el-upload action="#" list-type="picture-card" :auto-upload="false">
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
                    <span v-if="!disabled" class="el-upload-list__item-delete" @click="handleDownload(file)">
                      <el-icon>
                        <Download />
                      </el-icon>
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
          <el-form-item label="Vai trò: " :label-width="formLabelWidth">
            <el-select v-model="form.vt_ma" placeholder="Chọn vai trò">
              <el-option v-for="role in vaiTroData" :key="role.vt_ma" :label="role.vt_ten" :value="role.vt_ma" />
            </el-select>
          </el-form-item>
        </el-form>
        <template #footer>
          <div class="dialog-footer">
            <el-button @click="dialogFormEdit = false">Cancel</el-button>
            <el-button type="primary" @click="handleEdit">Confirm</el-button>
          </div>
        </template>
      </el-dialog>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import { deleteNguoiDung, getListNguoiDung } from '@/api/nguoidung';
  import { getListVaiTro } from '@/api/vaitro';
  import { ElMessage, ElMessageBox } from 'element-plus';
  import { Plus, ZoomIn, Download, Delete } from '@element-plus/icons-vue';
  import * as XLSX from 'xlsx';
  
  const search = ref('');
  const selectedGender = ref(null);
  const selectedRole = ref(null);
  const tableData = ref([]);
  const vaiTroData = ref([]);
  const dialogFormVisible = ref(false);
  const dialogFormEdit = ref(false);
  
  const form = ref({
    nd_username: '',
    nd_hoten: '',
    nd_gioitinh: null,
    nd_email: '',
    nd_sdt: '',
    nd_matkhau: '',
    nd_diachi: '',
    nd_ngaysinh: null,
    nd_hinh: '',
    vt_ma: null,
  });
  
  const currentPage = ref(1);
  const pageSize = ref(7);
  
  const filteredTableData = computed(() => {
    return tableData.value.filter(data => {
      const matchesSearch = !search.value ||
        data.nd_username.toLowerCase().includes(search.value.toLowerCase()) ||
        data.nd_hoten.toLowerCase().includes(search.value.toLowerCase());
      const matchesGender = selectedGender.value === null || data.nd_gioitinh === selectedGender.value;
      const matchesRole = selectedRole.value === null || data.vt_ma === selectedRole.value;
  
      return matchesSearch && matchesGender && matchesRole;
    });
  });
  
  const paginatedTableData = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredTableData.value.slice(start, end);
  });
  const isTableEmpty = computed(() => {
    return paginatedTableData.value.length === 0;
});
  onMounted(async () => {
    try {
      const data = await getListNguoiDung();
      tableData.value = data;
      const vtdata = await getListVaiTro();
      vaiTroData.value = vtdata;
    } catch (error) {
      console.error('Lỗi khi lấy dữ liệu:', error);
    }
  });
  
  const exportToExcel = () => {
    const ws = XLSX.utils.json_to_sheet(filteredTableData.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, 'users.xlsx');
  };
  
  const handleAdd = async () => {
    try {
      await addUser(form.value);
      ElMessage.success('Người dùng đã được thêm');
      dialogFormVisible.value = false;
      const data = await getListNguoiDung();
      tableData.value = data;
    } catch (error) {
      ElMessage.error('Lỗi khi thêm người dùng');
      console.error('Lỗi khi thêm người dùng:', error);
    }
  };
  
  const handleEdit = async () => {
    try {
      await updateUser(form.value);
      ElMessage.success('Người dùng đã được cập nhật');
      dialogFormEdit.value = false;
      const data = await getListNguoiDung();
      tableData.value = data;
    } catch (error) {
      ElMessage.error('Lỗi khi cập nhật người dùng');
      console.error('Lỗi khi cập nhật người dùng:', error);
    }
  };
  
  const handleDelete = (index, row) => {
    ElMessageBox.confirm('Bạn có chắc chắn muốn xóa người dùng này?', 'Cảnh báo', {
      confirmButtonText: 'Xóa',
      cancelButtonText: 'Hủy',
      type: 'warning',
    }).then(async () => {
      try {
        await deleteNguoiDung(row.nd_username);
        ElMessage.success('Người dùng đã được xóa');
        tableData.value.splice(index, 1);
      } catch (error) {
        ElMessage.error('Lỗi khi xóa người dùng');
        console.error('Lỗi khi xóa người dùng:', error);
      }
    });
  };
  
  const openEditDialog = (row) => {
    form.value = { ...row };
    dialogFormEdit.value = true;
  };
  
  const formatGender = (value) => {
    switch (value) {
      case 1:
        return 'Nữ';
      case 2:
        return 'Nam';
      case 0:
        return 'Không rõ';
      default:
        return 'Không xác định';
    }
  };
  
  const getTagType = (roleName) => {
    return roleName === 'Admin' ? 'danger' : 'info';
  };
  
  const handlePageChange = (page) => {
    currentPage.value = page;
  };
  
  const handlePageSizeChange = (size) => {
    pageSize.value = size;
    currentPage.value = 1; // Reset to the first page on page size change
  };
  </script>
  
  
  <style scoped>
  .app-container {
    padding: 20px;
  }
  
  .filter-controls {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .filter-controls .el-input,
  .filter-controls .el-select {
    margin-right: 10px;
  }
  
  .filter-controls .el-button {
    margin-left: 10px;
  }
  
  .dialog-footer {
    text-align: right;
  }
  </style>
  