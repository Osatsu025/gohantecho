'use client';

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { zodResolver } from "@hookform/resolvers/zod";
import axios from "@/lib/axios";
import { useRouter } from "next/navigation";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { z } from "zod";

const UpdateProfileComponent = () => {

    const formScheme = z.object({
      name: z.string().min(1, { message: 'ニックネームを入力してください' }),
      email: z.email({ message: '有効なメールアドレスではありません' }),
    });

    const router = useRouter();

    type FormValues = z.infer<typeof formScheme>;

    const form = useForm<FormValues>({
      resolver: zodResolver(formScheme),
      defaultValues: {
        name: '',
        email: '',
      },
    });

    useEffect(() => {
      const fetchUser = async () => {
        try {
          const { data: user } = await axios.get('/api/user');
          form.reset({
            name: user.name,
            email: user.email,
          });
        } catch (error) {
          console.error('ユーザー情報の取得に失敗しました:', error);
        }
      };
      fetchUser();
    }, [form.reset]);

    async function onSubmit(values: FormValues) {
      try {
        await axios.patch('/api/profile', values);
        
        router.refresh();
        toast.success('プロフィールを更新しました。');
      } catch (error: any) {
        if (error.response?.status === 422) {
          Object.keys(error.response.data.errors).forEach((key) => {
            const field = key as keyof FormValues;
            const message = error.response.data.errors[field].join(', ');
            form.setError(field, { type: 'server', message });
          });
        } else {
          form.setError('root.serverError', {
            type: 'server',
            message: '更新に失敗しました。もう一度お試しください'
          });
          toast.error('プロフィールの更新に失敗しました。');
          console.error('An unexpected error occurred:', error);
        }
      }
    };

  return (
    <Card className="w-full">
      <CardHeader>
        <CardTitle>プロフィール編集</CardTitle>
      </CardHeader>
      <CardContent>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8" id='profile-form'>
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

            {form.formState.errors.root?.serverError && (
              <p className="text-sm font-medium text-destructive">
                {form.formState.errors.root.serverError.message}
              </p>
            )}
          </form>
        </Form>
      </CardContent>
      <CardFooter className="flex-col gap-2">
        <Button type="submit" disabled={form.formState.isSubmitting} form="profile-form">
          {form.formState.isSubmitting ? '更新中…' : '更新'}
        </Button>
      </CardFooter>
    </Card>
  )
}

export default UpdateProfileComponent;