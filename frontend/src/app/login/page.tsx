'use client';

import { useRouter, useSearchParams } from 'next/navigation';
import Link from 'next/link';
import axios from '@/lib/axios';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { z } from "zod";
import { toast } from 'sonner';

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

const formScheme = z.object({
    email: z.email({message: '有効なメールアドレスではありません'}),
    password: z.string().min(1, { message: 'パスワードを入力してください' }),
})

export default function LoginPage() {

    const router = useRouter();
    const searchParams = useSearchParams();
    const redirect = searchParams.get('redirect');

    type FormValues = z.infer<typeof formScheme>;

    const form = useForm<FormValues>({
        resolver: zodResolver(formScheme),
        defaultValues: {
            email: "",
            password: "",
        },
    });

    async function onSubmit(values: FormValues) {

        try {
            // 1. CSRF Cookieを取得
            await axios.get('/sanctum/csrf-cookie');

            // 2. ログインAPIを呼び出し
            await axios.post('/api/login', values);

            // 3. ログイン後、redirectパラメータがあればそこへ、なければ'/'へリダイレクト
            router.push(redirect || '/');

        } catch (error: any) {
            // 4. エラーハンドリング
            if (error.response?.status === 422) {
                // react-hook-formにエラーをセット
                Object.keys(error.response.data.errors).forEach((key) => {
                    const field = key as keyof FormValues;
                    const message = error.response.data.errors[field].join(', ');
                    form.setError(field, { type: 'server', message });
                });
                toast.error('入力内容を確認してください。');
            } else {
                // その他のネットワークエラーなど
                form.setError('root.serverError', {
                    type: 'server',
                    message: 'ログインに失敗しました。もう一度お試しください'
                });
                toast.error('ログインに失敗しました。もう一度お試しください。');
                if (axios.isAxiosError(error)) {
                    console.error('Axios error:', error.response?.data || error.message);
                } else {
                    console.error('An unexpected error occurred:', error);
                }
            }
        }
    };

    return (
        <Card className='w-full max-w-sm'>
            <CardHeader>
                <CardTitle>ログイン</CardTitle>
                <CardAction>
                    {/* Linkにもredirectパラメータを引き継ぐ */}
                    <Button asChild>
                        <Link href={redirect ? `/sign-up?redirect=${redirect}` : '/sign-up'}>新規登録</Link>
                    </Button>
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

                        {form.formState.errors.root?.serverError && (
                            <p className='text-sm font-medium text-destructive'>
                                {form.formState.errors.root.serverError.message}
                            </p>
                        )}
                    </form>
                </Form>
            </CardContent>
            <CardFooter className='flex-col gap-2'>
                <Button type='submit' className='w-full' disabled={form.formState.isSubmitting} form='login-form'>
                    {form.formState.isSubmitting ? 'ログイン中…' : 'ログイン'}
                </Button>
            </CardFooter>
        </Card>
    )
}