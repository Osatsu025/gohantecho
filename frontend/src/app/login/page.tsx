'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import axios from '@/lib/axios'; // 設定済みのaxiosインスタンスをインポート

// エラー状態の型定義
type State = {
    errors?: {
        email?: string[];
        password?: string[];
    };
    message?: string;
} | undefined;

export default function LoginPage() {
    const router = useRouter();
    const [state, setState] = useState<State>();
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setIsLoading(true);
        setState(undefined); // 以前のエラーをクリア

        const formData = new FormData(event.currentTarget);
        const email = formData.get('email');
        const password = formData.get('password');

        try {
            // 1. CSRF Cookieを取得
            await axios.get('/sanctum/csrf-cookie');

            // 2. ログインAPIを呼び出し
            await axios.post('/login', {
                email,
                password,
            });

            // 3. ログイン成功後、ダッシュボードなどにリダイレクト
            // 必要であれば、ここでユーザー情報を再取得してからリダイレクトします。
            // 例: await mutateUser();
            router.push('/dashboard'); // 遷移先のパスを指定してください

        } catch (error: any) {
            // 4. エラーハンドリング
            if (error.response?.status === 422) {
                // バリデーションエラーの場合
                setState({
                    errors: error.response.data.errors,
                });
            } else {
                // その他のネットワークエラーなど
                setState({
                    message: 'ログインに失敗しました。もう一度お試しください。',
                });
                console.error('An unexpected error occurred:', error);
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            {/* Email Input */}
            <div>
                <label htmlFor="email">Email</label>
                <input id="email" type="email" name="email" required />
                {state?.errors?.email && (
                    <div style={{ color: 'red' }}>
                        {state.errors.email.map((error, i) => <p key={i}>{error}</p>)}
                    </div>
                )}
            </div>

            {/* Password Input */}
            <div>
                <label htmlFor="password">Password</label>
                <input id="password" type="password" name="password" required />
                 {state?.errors?.password && (
                    <div style={{ color: 'red' }}>
                        {state.errors.password.map((error, i) => <p key={i}>{error}</p>)}
                    </div>
                )}
            </div>

            {/* General Error Message */}
            {state?.message && (
                <div style={{ color: 'red' }}>
                    <p>{state.message}</p>
                </div>
            )}

            {/* Submit Button */}
            <div>
                <button type="submit" disabled={isLoading}>
                    {isLoading ? 'ログイン中...' : 'ログイン'}
                </button>
            </div>
        </form>
    );
}
