'use client';

import Link from 'next/link';
import { useRouter, useSearchParams } from 'next/navigation';
import axios from '@/lib/axios'; // 設定済みのaxiosインスタンスをインポート
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

export default function SignUpPage() {

  const router = useRouter();
  const searchParams = useSearchParams();
  const redirect = searchParams.get('redirect');

  const formScheme = z
  .object({
    name: z.string().min(1, { message: 'ニックネームを入力してください' }),
    email: z.email({ message: '有効なメールアドレスではありません' }),
    password: z.string().min(6, { message: 'パスワードは6文字以上で入力してください' }),
    passwordConfirmation: z.string().min(1, { message: '確認用のパスワードを入力してください' }),
  })
  .superRefine(({ password, passwordConfirmation }, ctx) => {
    if (password != passwordConfirmation) {
      ctx.addIssue({
        path: ['passwordConfirmation'],
        code: 'custom',
        message: 'パスワードが一致しません',
      });
    }
  });

  type FormValues = z.infer<typeof formScheme>;

  const form = useForm<FormValues>({
      resolver: zodResolver(formScheme),
      defaultValues: {
          name: '',
          email: '',
          password: '',
          passwordConfirmation: '',
      },
  });

  async function onSubmit(values: FormValues) {

      try {
          // CSRF保護を初期化
          await axios.get('/sanctum/csrf-cookie');

          const postData = {
            name: values.name,
            email: values.email,
            password: values.password,
            password_confirmation: values.passwordConfirmation,
          };

          const response = await axios.post('/api/register', postData);

          // 登録後、redirectパラメータがあればそこへ、なければ'/'へリダイレクト
          router.push(redirect || '/');

      } catch (error: any) {
          // 4. エラーハンドリング
          if (error.response?.status === 422) {
              // snake_caseのキーをcamelCaseに変換するマッピング
              const fieldMap: { [key: string]: keyof FormValues } = {
                  'password_confirmation': 'passwordConfirmation',
              };

              // react-hook-formにエラーをセット
              Object.keys(error.response.data.errors).forEach((key) => {
                  // マッピングが存在すればそれを使用し、なければ元のキーをそのまま使う
                  const fieldName = fieldMap[key] || (key as keyof FormValues);
                  if (Object.keys(form.getValues()).includes(fieldName)) {
                      const message = error.response.data.errors[key].join(', ');
                      form.setError(fieldName, { type: 'server', message });
                  }
              });
              toast.error('入力内容を確認してください。');
          } else {
              // その他のネットワークエラーなど
                form.setError('root.serverError', {
                    type: 'server',
                    message: '登録に失敗しました。もう一度お試しください'
                });
                toast.error('登録に失敗しました。もう一度お試しください。');
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
        <CardTitle>新規登録</CardTitle>
        <CardAction>
            {/* Linkにもredirectパラメータを引き継ぐ */}
            <Button asChild>
                <Link href={redirect ? `/login?redirect=${redirect}` : '/login'}>ログイン</Link>
            </Button>
        </CardAction>
    </CardHeader>
    <CardContent>
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className='space-y-8' id='sign-up-form'>
                <FormField
                  control={form.control}
                  name='name'
                  render={({ field }) => (
                      <FormItem>
                          <FormLabel>ニックネーム</FormLabel>
                          <FormControl>
                              <Input type='text' placeholder='馬井ごはん' {...field} />
                          </FormControl>
                          <FormMessage />
                      </FormItem>
                  )}
                />
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
                <FormField 
                  control={form.control}
                  name='passwordConfirmation'
                  render={({ field }) => (
                      <FormItem>
                          <FormLabel>パスワード(確認)</FormLabel>
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
        <Button type='submit' className='w-full' disabled={form.formState.isSubmitting} form='sign-up-form'>
            {form.formState.isSubmitting ? '登録中…' : '登録'}
        </Button>
    </CardFooter>
</Card>
  )
}