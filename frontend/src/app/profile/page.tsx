import DeleteUserComponent from "./_components/delete-user-form";
import UpdatePasswordComponent from "./_components/update-password-form";
import UpdateProfileComponent from "./_components/update-profile-information-form";


export default function ProfilePage() {
  return (
    <div className="flex w-full max-w-lg flex-col gap-8">
      <UpdateProfileComponent />
      <UpdatePasswordComponent />
      <DeleteUserComponent />
    </div>
  )
}