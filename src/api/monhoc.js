import axios from 'axios';
const API = 'http://127.0.0.1:8000';
export function getListMonHoc() {
  return axios.get(API+'/listmonhoc.php')
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch users:', error);
      throw error;
    });
}

export function addMonHoc(monHoc) {
    return axios.post(`${API}/monhocadd.php`, monHoc)
        .then(response => response.data)
        .catch(error => {
            console.error('Failed to add Khoi Lop:', error);
            throw error;
        });
  }
  
  export function updateMonHoc(monHoc) {
    return axios.post(`${API}/monhocedit.php`, monHoc)
      .then(response => response.data)
      .catch(error => {
        console.error('Failed to update MonHoc:', error);
        throw error;
      });
  }
  
  
  export function deleteMonHoc(mh_ma) {
    return axios.post(`${API}/monhocdelete.php`, { mh_ma })
      .then(response => response.data)
      .catch(error => {
        console.error('Failed to delete MonHoc:', error);
        throw error;
      });
  }