'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import axios from '@/lib/axios'; // 設定済みのaxiosインスタンスをインポート
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { z } from "zod";

import { Button } from '@/components/ui/button';
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";

// エラー状態の型定義
type State = {
    errors?: {
        email?: string[];
        password?: string[];
    };
    message?: string;
} | undefined;

const formScheme = z.object({
    email: z.email({message: '有効なメールアドレスではありません'}),
    password: z.string().min(1, { message: 'パスワードを入力してください' }),
})

export default function LoginPage() {
    const router = useRouter();
    const [state, setState] = useState<State>();
    const [isLoading, setIsLoading] = useState(false);

    const form = useForm<z.infer<typeof formScheme>>({
        resolver: zodResolver(formScheme),
        defaultValues: {
            email: "",
            password: "",
        },
    });

    async function onSubmit(values: z.infer<typeof formScheme>) {
        
        setIsLoading(true);
        setState(undefined);
        form.clearErrors();

        try {
            // 1. CSRF Cookieを取得
            await axios.get('/sanctum/csrf-cookie');

            // 2. ログインAPIを呼び出し
            await axios.post('/login', values);

            // 3. ログイン成功後、ダッシュボードなどにリダイレクト
            router.push('/dashboard'); // 遷移先のパスを指定してください

        } catch (error: any) {
            // 4. エラーハンドリング
            if (error.response?.status === 422) {
                // バリデーションエラーの場合
                setState({
                    errors: error.response.data.errors,
                });
                // react-hook-formにエラーをセット
                Object.keys(error.response.data.errors).forEach((key) => {
                    const field = key as 'email' | 'password';
                    const message = error.response.data.errors[field].join(', ');
                    form.setError(field, { type: 'server', message });
                })
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
        <Card className='w-full max-w-sm'>
            <CardHeader>
                <CardTitle>ログイン</CardTitle>
                <CardAction>
                    <Button asChild><Link href='/sign-up'>新規登録</Link></Button>
                </CardAction>
            </CardHeader>
            <CardContent>
                <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className='space-y-8' id='login-form'>
                        <FormField
                            control={form.control}
                            name='email'
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>メールアドレス</FormLabel>
                                    <FormControl>
                                        <Input type='email' placeholder='gohan@example.com' {...field} />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField 
                            control={form.control}
                            name='password'
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>パスワード</FormLabel>
                                    <FormControl>
                                        <Input type='password' {...field} />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {state?.message && (
                            <p className='text-sm font-medium text-destructive'>{state.message}</p>
                        )}
                    </form>
                </Form>
            </CardContent>
            <CardFooter className='flex-col gap-2'>
                <Button type='submit' className='w-full' disabled={isLoading} form='login-form'>
                    {isLoading ? 'ログイン中…' : 'ログイン'}
                </Button>
            </CardFooter>
        </Card>
    )
}