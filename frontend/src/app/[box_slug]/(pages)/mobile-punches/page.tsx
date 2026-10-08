'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Loader2 } from 'lucide-react';
import { toast } from 'sonner';
import { format } from 'date-fns';

interface Punch {
  id: number;
  user_name: string;
  device_name: string;
  punched_at: string;
  action: string;
  latitude: string;
  longitude: string;
}

export default function MobilePunches() {
  const { box_slug } = useParams();
  const boxSlug = box_slug as string;

  const [punches, setPunches] = useState<Punch[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchPunches = async () => {
    setLoading(true);
    try {
      const res = await apiClient.get(`/${boxSlug}/mobile-attendance/punches`);
      setPunches(res.data?.data || res.data || []);
    } catch (error) {
      console.error('Error fetching punches:', error);
      toast.error('Failed to fetch mobile punches.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchPunches();
    }
  }, [boxSlug]);

  const getActionBadge = (action: string) => {
    const a = (action || '').replace(/<[^>]+>/g, '').trim().toLowerCase();
    if (a.includes('in')) return <Badge className="bg-emerald-100 text-emerald-700 hover:bg-emerald-100">Punch In</Badge>;
    if (a.includes('out')) return <Badge className="bg-rose-100 text-rose-700 hover:bg-rose-100">Punch Out</Badge>;
    return <Badge variant="outline" className="capitalize">{a}</Badge>;
  };

  return (
    <div className="p-6 space-y-6 max-w-[1600px] mx-auto">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Mobile Punches</h1>
          <p className="text-sm text-muted-foreground">View real-time attendance punches from employee mobile devices.</p>
        </div>
      </div>

      <Card>
        <CardHeader className="p-4 border-b">
          <CardTitle className="text-lg">Punch Logs</CardTitle>
        </CardHeader>
        <div className="overflow-auto w-full max-h-[70vh]">
          <Table>
            <TableHeader className="bg-muted/50 sticky top-0 z-10 shadow-sm">
              <TableRow className="bg-muted hover:bg-muted">
                <TableHead className="w-12 text-center">No</TableHead>
                <TableHead>Employee Name</TableHead>
                <TableHead>Device Name</TableHead>
                <TableHead>Punch Time</TableHead>
                <TableHead>Action</TableHead>
                <TableHead>Location (Lat/Lng)</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={6} className="text-center py-10">
                    <div className="flex justify-center items-center">
                      <Loader2 className="h-6 w-6 animate-spin text-muted-foreground mr-2" />
                      <span className="text-muted-foreground">Loading punches...</span>
                    </div>
                  </TableCell>
                </TableRow>
              ) : punches.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={6} className="text-center py-10 text-muted-foreground">
                    No mobile punches recorded yet.
                  </TableCell>
                </TableRow>
              ) : (
                punches.map((punch, index) => {
                  const location = punch.latitude && punch.longitude ? `${punch.latitude}, ${punch.longitude}` : '-';
                  return (
                    <TableRow key={punch.id}>
                      <TableCell className="text-center">{index + 1}</TableCell>
                      <TableCell className="font-medium" dangerouslySetInnerHTML={{ __html: punch.user_name || '-' }} />
                      <TableCell dangerouslySetInnerHTML={{ __html: punch.device_name || '-' }} />
                      <TableCell>{punch.punched_at ? format(new Date(punch.punched_at), 'yyyy-MM-dd HH:mm:ss') : '-'}</TableCell>
                      <TableCell>{getActionBadge(punch.action)}</TableCell>
                      <TableCell>
                        {location !== '-' ? (
                          <a href={`https://maps.google.com/?q=${punch.latitude},${punch.longitude}`} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">
                            {location}
                          </a>
                        ) : (
                          location
                        )}
                      </TableCell>
                    </TableRow>
                  );
                })
              )}
            </TableBody>
          </Table>
        </div>
      </Card>
    </div>
  );
}
