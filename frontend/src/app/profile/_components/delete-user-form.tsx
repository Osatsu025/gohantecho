'use client';

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import axios from "@/lib/axios";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogTitle, AlertDialogTrigger, AlertDialogFooter, AlertDialogHeader } from "@/components/ui/alert-dialog";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { useState } from "react";

const DeleteUserComponent = () => {

  const router = useRouter();
  const [ isDeleteMenus, setIsDeleteMenus ] = useState(true);

  async function handleDelete() {
    try {
      const response = await axios.delete('/api/profile', {
        data: {
          is_delete_menus: isDeleteMenus,
        }
      });
      
      if (response.data.status !== 'user-deleted') {
        throw new Error('API did not confirm user deletion.');
      }

      toast.success('アカウントを削除しました。');
      router.push('/login');

    } catch (error: any) {
        toast.error('アカウントの削除に失敗しました。');
        console.error('An unexpected error occurred:', error);
    }
  }

  return (
    <Card className="w-full">
      <CardHeader>
        <CardTitle>アカウント削除</CardTitle>
      </CardHeader>
      <CardContent>
          <p>アカウントを削除すると、すべてのデータが消えます</p>
      </CardContent>
      <CardFooter className="flex-col gap-2">
        <AlertDialog>
          <AlertDialogTrigger asChild>
            <Button variant="destructive">削除</Button>
          </AlertDialogTrigger>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>アカウント削除</AlertDialogTitle>
              <AlertDialogDescription>
                アカウントを一度削除すると元に戻せません。本当に削除しますか？
              </AlertDialogDescription>
              <div className="flex items-center space-x-2 pt-4">
                <Label htmlFor="is-delete-menus" className="text-sm font-medium lending-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">すべてのメニューを削除する</Label>
                <Checkbox
                    id="is-delete-menus"
                    checked={isDeleteMenus}
                    onCheckedChange={(checked) => setIsDeleteMenus(Boolean(checked))}
                />
              </div>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>キャンセル</AlertDialogCancel>
              <Button variant="destructive" onClick={handleDelete}>削除</Button>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      </CardFooter>
    </Card>
  )
}

export default DeleteUserComponent;