import axios from 'axios';

const API = 'http://127.0.0.1:8000';
export function getListNguoiDung() {
  return axios.get(API+'/listnguoidung.php')
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch users:', error);
      throw error;
    });
}
export function getNguoiDungDetail(ndUsername) {
    return axios.get(`${API}/nguoidungdetail.php`, {
      params: { nd_username: ndUsername }
    })
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch details:', error);
      throw error;
    });
  }

  export function addNguoiDung(nguoiDung) {
    return axios.post(`${API}/nguoidungadd.php`, nguoiDung)
        .then(response => response.data)
        .catch(error => {
            console.error('Failed to add nguoidung:', error);
            throw error;
        });
  }
  
  export function updateNguoiDung(nguoiDung) {
    return axios.post(`${API}/nguoidungedit.php`, nguoiDung)
      .then(response => response.data)
      .catch(error => {
        console.error('Failed to update nugoidunf:', error);
        throw error;
      });
  }
  


  export function deleteNguoiDung(nd_username) {
    return axios.post(`${API}/nguoidungdelete.php`, { nd_username })
      .then(response => response.data)
      .catch(error => {
        console.error('Failed to delete MonHoc:', error);
        throw error;
      });
  }