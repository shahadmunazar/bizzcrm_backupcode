import { redirect } from 'next/navigation';

export default async function TenantPage({ params }: { params: Promise<{ box_slug: string }> }) {
  const resolvedParams = await params;
  const boxSlug = resolvedParams?.box_slug;

  if (!boxSlug || boxSlug === 'undefined') {
    redirect('/pages/auth/login');
  }

  redirect(`/${boxSlug}/dashboard/admin`);
}
