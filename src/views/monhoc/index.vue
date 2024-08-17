<template>
    <div class="app-container">
        <el-row :gutter="5">  <el-col  :span="7">
                <el-input v-model="search" placeholder="Tìm kiếm môn học" />
            </el-col>
            <el-col :span="6">
               
               <div class="filter-container">
                   <el-select v-model="selectedKhoiLop" placeholder="Chọn khối lớp" >
                       <el-option value="" label="Tất cả"></el-option>
                       <el-option v-for="item in khoiLopData" :key="item.kl_ma" :label="item.kl_ten"
                           :value="item.kl_ma" />
                   </el-select>
               </div>
           </el-col>
            <el-col :offset="5" :span="3">
                <el-button @click="dialogFormVisible = true" type="primary"
                    :icon="Plus">Thêm môn học</el-button>
            </el-col>
            <el-col :span="3">
                <el-button  :disabled="isTableEmpty" style="background-color: #35ba9b;" @click="exportToExcel" type="primary"
                    :icon="Download">Xuất excel</el-button>
            </el-col>
          
        </el-row>
   

        <!-- Class Filter -->


        <el-table :data="paginatedData" style="width: 100%;">
            <el-table-column label="STT" prop="mh_ma" />
            <el-table-column label="Môn học">
                <template #default="scope">
                    <div v-if="editingRow === scope.$index">
                        <el-input v-model="editName" size="small" />
                    </div>
                    <div v-else>
                        {{ scope.row.mh_ten }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="Thuộc lớp">
                <template #default="scope">
                    <div v-if="editingRow === scope.$index">
                        <el-select v-model="editKhoiLop" placeholder="Select" size="small" style="width: 240px">
                            <el-option v-for="item in khoiLopData" :key="item.kl_ma" :label="item.kl_ten"
                                :value="item.kl_ma" />
                        </el-select>
                    </div>
                    <div v-else>
                        {{ scope.row.kl_ten }}
                    </div>
                </template>
            </el-table-column>

            <el-table-column align="right">
                <template #header>
                 
                </template>
                <template #default="scope">
                    <el-button type="primary" v-if="editingRow === scope.$index" size="small"
                        @click="handleConfirm(scope.row)">
                        Xác nhận
                    </el-button>
                    <el-button type="warning" plain v-else size="small" @click="handleEdit(scope.$index, scope.row)">
                        Chỉnh sửa
                    </el-button>
                    <el-button size="small" type="danger" @click="handleDelete(scope.$index, scope.row)">
                        Xoá
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- Phân trang -->
        <div class="pagination-container">
            <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize"
                :page-sizes="[5, 10, 20, 50, 100]" :background="true" layout="total, sizes, prev, pager, next, jumper"
                :total="tableData.length" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
        </div>

        <el-dialog v-model="dialogFormVisible" title="Thêm môn học" width="500">
            <el-form :model="form">
                <el-form-item label="Tên môn học: " :label-width="formLabelWidth">
                    <el-input v-model="form.name" autocomplete="off" />
                </el-form-item>
                <el-form-item label="Chọn khối lớp:" :label-width="formLabelWidth">
                    <el-select v-model="form.kl_ma" placeholder="Chọn khối lớp">
                        <el-option v-for="khoiLop in khoiLopData" :key="khoiLop.kl_ma" :label="khoiLop.kl_ten"
                            :value="khoiLop.kl_ma" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogFormVisible = false">Cancel</el-button>
                    <el-button type="primary" @click="handleAdd">
                        Confirm
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>
<script setup>
import { ref, computed, onMounted, watch  } from 'vue';
import { getListKhoiLop } from '@/api/khoilop';
import { getListMonHoc, addMonHoc, updateMonHoc, deleteMonHoc } from '@/api/monhoc';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Download } from '@element-plus/icons-vue';
import * as XLSX from 'xlsx';

const search = ref('');
const tableData = ref([]);
const khoiLopData = ref([]);
const editingRow = ref(null);
const editName = ref('');
const editKhoiLop = ref('');
const selectedKhoiLop = ref('');

const dialogFormVisible = ref(false);

const form = ref({
    name: '',
    kl_ma: null,
});

// Trạng thái cho phân trang
const currentPage = ref(1);
const pageSize = ref(10);

const filterTableData = computed(() =>
    tableData.value.filter(
        data =>
            (!search.value || data.mh_ten.toLowerCase().includes(search.value.toLowerCase())) &&
            (!selectedKhoiLop.value || data.kl_ma === selectedKhoiLop.value)
    )
);
const isTableEmpty = computed(() => {
    return filterTableData.value.length === 0;
});
// Tính toán dữ liệu hiển thị dựa trên trang hiện tại và số lượng phần tử trên mỗi trang
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filterTableData.value.slice(start, end);
});

const loading = ref(false);

onMounted(async () => {
    loading.value = true;
    try {
        const data = await getListMonHoc();
        tableData.value = data;
        const kldata = await getListKhoiLop();
        khoiLopData.value = kldata;
        console.log(khoiLopData.value);
    } catch (error) {
        console.error('Lỗi khi lấy dữ liệu:', error);
    } finally {
        loading.value = false;
    }
});

const handleSizeChange = (newSize) => {
    pageSize.value = newSize;
};

const handleCurrentChange = (newPage) => {
    currentPage.value = newPage;
};

const handleAdd = async () => {
    if (form.value.name.trim() !== '' && form.value.kl_ma) {
        try {
            const response = await addMonHoc({ mh_ten: form.value.name, kl_ma: form.value.kl_ma });
            console.log('Dữ liệu trả về:', response.data);

            if (response.success) {
                const newEntry = {
                    mh_ma: response.data.mh_ma,
                    mh_ten: response.data.mh_ten,
                    kl_ma: form.value.kl_ma,
                    kl_ten: khoiLopData.value.find(khoiLop => khoiLop.kl_ma === form.value.kl_ma)?.kl_ten
                };
                tableData.value.unshift(newEntry);
                dialogFormVisible.value = false;
                ElMessage({
                    message: 'Thêm thành công!',
                    type: 'success',
                });
            } else {
                console.error('Lỗi khi thêm:', response.message);
                ElMessage.error('Có lỗi xảy ra.');
            }
        } catch (error) {
            console.error('Lỗi khi thêm:', error);
            ElMessage.error('Có lỗi xảy ra!');
        }
    } else {
        ElMessage.error('Tên môn học và khối lớp không được để trống!');
    }
};

const handleEdit = (index, row) => {
    editingRow.value = index;
    editName.value = row.mh_ten;
    editKhoiLop.value = row.kl_ma;
};

const handleConfirm = async row => {
    if (editingRow.value !== null) {
        try {
            const updatedRow = { ...row, mh_ten: editName.value, kl_ma: editKhoiLop.value };
            console.log('Cập nhật:', updatedRow);

            const response = await updateMonHoc(updatedRow);
            console.log('Kết quả phản hồi:', response);

            if (response.success) {
                tableData.value = tableData.value.map((item, index) =>
                    index === editingRow.value ? updatedRow : item
                );
                console.log(tableData.value);

                editingRow.value = null;
                editName.value = '';
                ElMessage({
                    message: 'Cập nhật thành công',
                    type: 'success',
                });
            } else {
                console.error('Lỗi khi cập nhật:', response.message);
                ElMessage.error('Có lỗi xảy ra.');
            }
        } catch (error) {
            console.error('Lỗi khi cập nhật:', error);
            ElMessage.error('Có lỗi xảy ra!');
        }
    }
};

const handleDelete = async (index, row) => {
    try {
        await ElMessageBox.confirm(
            'Môn học sẽ bị xoá vĩnh viễn. Bạn có chắc chắn muốn tiếp tục?',
            'Cảnh báo',
            {
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                type: 'warning',
            }
        );

        const response = await deleteMonHoc(row.mh_ma);
        if (response.success) {
            tableData.value = tableData.value.filter((item, i) => i !== index);
            ElMessage({
                message: 'Xóa thành công!',
                type: 'success',
            });
        } else {
            console.error('Lỗi khi xóa:', response.message);
            ElMessage.error('Không thể xóa môn học này!');
        }
    } catch (error) {
        if (error !== 'cancel') {
            console.error('Lỗi khi xóa:', error);
            ElMessage.error('Có lỗi xảy ra!');
        }
    }
};
watch([search, selectedKhoiLop], () => {
    currentPage.value = 1;
});


const exportToExcel = () => {
    // Prepare the data for export
    const worksheet = XLSX.utils.json_to_sheet(filterTableData.value);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Môn Học');

    // Generate buffer and create download link
    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const data = new Blob([excelBuffer], { type: EXCEL_TYPE });
    const now = new Date();
    const formattedDate = now.toISOString().slice(0, 19).replace(/T|:/g, '-'); // Format: YYYY-MM-DD-HH-MM-SS
    const fileName = `MonHoc_${formattedDate}.xlsx`;
    // Create a download link and click it
    const link = document.createElement('a');
    link.href = URL.createObjectURL(data);
    link.download = fileName;
    link.click();
};

const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';

</script>
<style scoped>
.app-container {
    margin: 20px;
}

.dialog-footer {
    text-align: right;
    margin: 0;
    padding: 0;
}

.pagination-container {
    position: fixed;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    padding: 10px 20px;
    background: white;
    box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
}

.filter-container {
    margin-bottom: 20px;
}
</style>
