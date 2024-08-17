import axios from 'axios';

const API = 'http://127.0.0.1:8000';
export function getListBinhLuan(bvMa) {
    return axios.get(`${API}/listbinhluan.php`, { params: { bv_ma: bvMa } })
      .then(response => response.data)
      .catch(error => {
        console.error('Failed to fetch comments:', error);
        throw error;
      });
}
