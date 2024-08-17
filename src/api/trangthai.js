import axios from 'axios';
const API = 'http://127.0.0.1:8000';
export function getListTrangThai() {
  return axios.get(API+'/listtrangthai.php')
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch users:', error);
      throw error;
    });
}

export function addKhoiLop(khoiLop) {
  return axios.post(`${API}/khoilopadd.php`, khoiLop)
      .then(response => response.data)
      .catch(error => {
          console.error('Failed to add Khoi Lop:', error);
          throw error;
      });
}

export function updateKhoiLop(khoiLop) {
  return axios.post(`${API}/khoilopedit.php`, khoiLop)
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to update Khoi Lop:', error);
      throw error;
    });
}


export function deleteKhoiLop(kl_ma) {
  return axios.post(`${API}/khoilopdelete.php`, { kl_ma })
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to delete Khoi Lop:', error);
      throw error;
    });
}