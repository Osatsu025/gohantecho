import axios from "axios";

const isServer = typeof window === 'undefined';

// サーバサイドとクライアントサイドで接続先のURLを切り替える
const baseURL = isServer
  ? process.env.BACKEND_SERVER_URL
  : process.env.NEXT_PUBLIC_BACKEND_URL;

axios.defaults.withCredentials = true;

const axiosInstance = axios.create({
  baseURL: baseURL,
  withXSRFToken: true,
  xsrfHeaderName: "X-XSRF-TOKEN",
  withCredentials: true,
});

export default axiosInstance;