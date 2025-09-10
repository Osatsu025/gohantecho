import axios from "axios";

// サーバサイドとクライアントサイドで接続先のURLを切り替える
const baseURL = process.env.BACKEND_SERVER_URL || process.env.NEXT_PUBLIC_BACKEND_URL;

axios.defaults.baseURL = baseURL;
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.xsrfHeaderName = "X-XSRF-TOKEN";

export default axios;
