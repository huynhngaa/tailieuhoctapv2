import axios from 'axios';

const API = 'http://127.0.0.1:8000';
export function getDanhMuc() {
  return axios.get(API+'/danhmuc.php')
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch users:', error);
      throw error;
    });
}

