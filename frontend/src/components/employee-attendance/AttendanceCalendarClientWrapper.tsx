'use client';

import React from 'react';
import dynamic from 'next/dynamic';

const AttendanceCalendar = dynamic(
  () => import('./AttendanceCalendar'),
  { ssr: false }
);

export default function AttendanceCalendarClientWrapper({ boxSlug }: { boxSlug: string }) {
  return <AttendanceCalendar boxSlug={boxSlug} />;
}
