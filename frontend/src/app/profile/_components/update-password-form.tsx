'use client';

import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { zodResolver } from "@hookform/resolvers/zod";
import axios from "@/lib/axios";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { Input } from "@/components/ui/input";
import z from "zod";
import { Button } from "@/components/ui/button";

const UpdatePasswordComponent = () => {

  const formScheme = z.object({
    currentPassword: z.string().min(1, { message: 'パスワードを入力してください' }),
    newPassword: z.string().min(6, { message: 'パスワードは6文字以上で入力してください' }),
    newPasswordConfirmation: z.string().min(1, { message: '確認用のパスワードを入力してください' }),
  })
  .superRefine(({ newPassword, newPasswordConfirmation }, ctx) => {
    if (newPassword != newPasswordConfirmation) {
      ctx.addIssue({
        path: ['newPasswordConfirmation'],
        code: 'custom',
        message: 'パスワードが一致しません',
      });
    }
  });

  const router = useRouter();

  type FormValues = z.infer<typeof formScheme>;

  const form = useForm<FormValues>({
    resolver: zodResolver(formScheme),
    defaultValues: {
      currentPassword: '',
      newPassword: '',
      newPasswordConfirmation: '',
    },
  });

  async function onSubmit(values: FormValues) {
    try {
      const postData = {
        current_password: values.currentPassword,
        password: values.newPassword,
        password_confirmation: values.newPasswordConfirmation,
      };

      await axios.put('/api/user/password', postData);

      router.refresh();
      toast.success('パスワードを更新しました');

    } catch (error: any) {
      if (error.response?.status === 422) {
        const fieldMap: { [key: string]: keyof FormValues } = {
          'current_password': 'currentPassword',
          'password': 'newPassword',
        };

        Object.keys(error.response.data.errors).forEach((key) => {
          const fieldName = fieldMap[key] || (key as keyof FormValues);
          if (Object.keys(form.getValues()).includes(fieldName)) {
            const message = error.response.data.errors[key].join(', ');
            form.setError(fieldName, { type: 'server', message });
          }
        });
        toast.error('入力内容を確認してください');
      } else {
        form.setError('root.serverError', {
          type: 'server',
          message: '登録に失敗しました。もう一度お試しください'
        });
        toast.error('登録に失敗しました。もう一度お試しください');
        if (axios.isAxiosError(error)) {
          console.error('Axios error:', error.response?.data || error.message);
        } else {
          console.error('An unexpected error occurred:', error);
        }
      }
    }
  };

  return (
    <Card className="w-full">
      <CardHeader>
        <CardTitle>パスワード更新</CardTitle>
      </CardHeader>
      <CardContent>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8" id="password-form">
            <FormField 
              control={form.control}
              name='currentPassword'
              render={({ field }) => (
                <FormItem>
                  <FormLabel>現在のパスワード</FormLabel>
                  <FormControl>
                    <Input type='password' {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField 
              control={form.control}
              name='newPassword'
              render={({ field }) => (
                <FormItem>
                  <FormLabel>新しいパスワード</FormLabel>
                  <FormControl>
                    <Input type='password' {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField 
              control={form.control}
              name='newPasswordConfirmation'
              render={({ field }) => (
                <FormItem>
                  <FormLabel>新しいパスワード(確認用)</FormLabel>
                  <FormControl>
                    <Input type='password' {...field} />
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
        <Button type="submit" disabled={form.formState.isSubmitting} form="password-form">
          {form.formState.isSubmitting ? '更新中…' : '更新'}
        </Button>
      </CardFooter>
    </Card>
  )
}

export default UpdatePasswordComponent;