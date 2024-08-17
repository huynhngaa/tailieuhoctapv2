<template>
    <div class="app-container">
        <el-button @click="dialogFormVisible = true" type="primary" :icon="Plus">Thêm môn học</el-button>

        <el-table :data="paginatedData" style="width: 100%; height: 80vh;" @row-click="handleRowClick">
            <el-table-column label="STT" prop="bv_ma" width="80" />

            <el-table-column label="Vai trò" prop="tt_ma" width="150">
                <template #default="scope">
                    <el-tag :key="scope.row.tt_ma" :type="getTagType(scope.row.tt_ma)" effect="light">
                        {{ scope.row.tt_ten ? scope.row.tt_ten : 'Chờ duyệt' }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Tiêu đề" prop="bv_tieude" width="350" />
            <el-table-column label="Ngày đăng" prop="bv_ngaydang" width="150" />
            <el-table-column label="Tác giả" prop="nd_hoten" width="250" />

            <el-table-column fixed="right" align="right" min-width="180">
                <template #default="scope">
                    <el-button v-if="scope.row.tt_ma == 5" type="info" size="small" @click.stop="openEditDialog(scope.row)">
                        Hiện
                    </el-button>
                    <el-button v-else-if="scope.row.tt_ma && scope.row.tt_ma != 4" type="success" size="small"
                        @click.stop="openEditDialog(scope.row)">
                        Ẩn
                    </el-button>
                    <el-button v-else-if="scope.row.tt_ma && scope.row.tt_ma == 4" type="primary" size="small"
                        @click.stop="openEditDialog(scope.row)">
                        Khôi phục
                    </el-button>
                    <el-button type="warning" size="small" @click.stop="openEditDialog(scope.row)">
                        Sửa
                    </el-button>
                    <el-button size="small" type="danger" @click.stop="handleDelete(scope.$index, scope.row)">
                        Xoá
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- Pagination Controls -->
        <el-pagination
            v-model:current-page="currentPage"
            :page-size="pageSize"
            :total="tableData.length"
            layout="prev, pager, next"
            @current-change="handlePageChange"
            style="margin-top: 20px; text-align: center;"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { deleteNguoiDung, getListNguoiDung } from '@/api/nguoidung';
import { getListVaiTro } from '@/api/vaitro';
import { getListMonHoc, addMonHoc, updateMonHoc, deleteMonHoc } from '@/api/monhoc';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus } from '@element-plus/icons-vue';
import { getListBaiVietAdmin } from '@/api/baiviet';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const search = ref('');
const tableData = ref([]);
const currentPage = ref(1);
const pageSize = ref(10);
const dialogFormVisible = ref(false);
const dialogFormEdit = ref(false);
const dialogVisible = ref(false);
const selectedArticle = ref({});

const form = ref({
    name: '',
    kl_ma: null,
});

const filterTableData = computed(() =>
    tableData.value.filter(
        data =>
            !search.value ||
            data.kl_ten.toLowerCase().includes(search.value.toLowerCase()) ||
            data.mh_ten.toLowerCase().includes(search.value.toLowerCase())
    )
);

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filterTableData.value.slice(start, end);
});

const loading = ref(false);

onMounted(async () => {
    loading.value = true;
    try {
        const data = await getListBaiVietAdmin();
        tableData.value = data;
    } catch (error) {
        console.error('Lỗi khi lấy dữ liệu:', error);
    } finally {
        loading.value = false;
    }
});

const openEditDialog = (row) => {
    form.value = { ...row };
    dialogFormEdit.value = true;
};

const handlePageChange = (newPage) => {
    currentPage.value = newPage;
};

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

const handleDelete = async (index, row) => {
    try {
        await ElMessageBox.confirm(
            'Người dùng sẽ bị xoá vĩnh viễn. Bạn có chắc chắn muốn tiếp tục?',
            'Cảnh báo',
            {
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                type: 'warning',
            }
        );

        const response = await deleteNguoiDung(row.nd_username);
        if (response.success) {
            tableData.value = tableData.value.filter((item, i) => i !== index);
            ElMessage({
                message: 'Xóa thành công!',
                type: 'success',
            });
        } else {
            ElMessage.error('Không thể xóa người dùng này!');
        }
    } catch (error) {
        if (error === 'cancel') {
            ElMessage({
                type: 'info',
                message: 'Xóa đã bị hủy!',
            });
        } else {
            console.error('Lỗi khi xóa:', error);
            ElMessage.error('Có lỗi xảy ra!');
        }
    }
};

const handleRowClick = (row) => {
    router.push({ name: 'ChiTietBaiVietQuanLy', params: { bv_ma: row.bv_ma } });
};

</script>
