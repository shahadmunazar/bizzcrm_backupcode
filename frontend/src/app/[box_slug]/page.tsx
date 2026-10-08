import { redirect } from 'next/navigation';

export default function TenantPage({ params }: { params: { box_slug: string } }) {
  redirect(`/${params.box_slug}/dashboard/admin`);
}
