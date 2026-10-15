'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Calendar, Clock, Loader2, Save, Info, RefreshCw } from 'lucide-react';

export default function AttendanceModuleSettingsPage() {
  const { box_slug } = useParams();
  const boxSlug = box_slug as string;

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  // Form state
  const [dateTime, setDateTime] = useState('');
  const [creditMode, setCreditMode] = useState<'last_day_of_every_month' | 'start_day_of_every_month' | 'specific_number' | ''>('');
  const [creditTime, setCreditTime] = useState('');
  const [startDay, setStartDay] = useState('');
  const [lastDay, setLastDay] = useState('');
  const [selectedDay, setSelectedDay] = useState<number | null>(null);

  const fetchSettings = async () => {
    setLoading(true);
    try {
      const res = await apiClient.get(`/${boxSlug}/attendace-module/get`);
      if (res.success && res.data) {
        const d = res.data;
        setDateTime(d.attendace_module_date || '');
        setCreditMode(d.leave_credit_mode || 'last_day_of_every_month');
        setCreditTime(d.leave_credit_time || '10:00 AM');
        setStartDay(d.start_day_of_every_month ? String(d.start_day_of_every_month) : '');
        setLastDay(d.last_day_of_every_month ? String(d.last_day_of_every_month) : '');
        if (d.leave_credit_selected_days) {
          setSelectedDay(parseInt(d.leave_credit_selected_days, 10));
        } else if (d.attendace_module_from_date) {
          setSelectedDay(parseInt(d.attendace_module_from_date, 10));
        }
      }
    } catch (error: any) {
      console.error('Failed to fetch attendance module settings:', error);
      toast.error('Failed to load settings. Please make sure you have appropriate permissions.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchSettings();
    }
  }, [boxSlug]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!dateTime) {
      toast.error('Please select date and time.');
      return;
    }
    if (!creditMode) {
      toast.error('Please select credit mode.');
      return;
    }
    if (!creditTime) {
      toast.error('Please enter credit time.');
      return;
    }
    if (creditMode === 'specific_number' && !selectedDay) {
      toast.error('Please select a day number for specific number mode.');
      return;
    }

    setSaving(true);
    try {
      const payload: any = {
        date_time: dateTime,
        credit_mode: creditMode,
        credit_time: creditTime,
        selected_days: creditMode === 'specific_number' && selectedDay ? String(selectedDay) : null,
      };

      if (creditMode === 'start_day_of_every_month' && startDay) {
        payload.from_date = parseInt(startDay, 10);
      } else if (creditMode === 'last_day_of_every_month' && lastDay) {
        payload.to_date = parseInt(lastDay, 10);
      } else if (creditMode === 'specific_number' && selectedDay) {
        payload.from_date = selectedDay;
      }

      const res = await apiClient.post(`/${boxSlug}/attendace-module/save`, payload);
      if (res.success !== false) {
        toast.success(res.message || 'Attendance module settings saved successfully.');
        fetchSettings();
      } else {
        toast.error(res.message || 'Failed to save settings.');
      }
    } catch (error: any) {
      console.error('Failed to save settings:', error);
      toast.error(error.response?.data?.message || error.message || 'Failed to save settings.');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="flex h-[70vh] items-center justify-center">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-muted-foreground font-medium">Loading Attendance Module Settings...</span>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6 max-w-4xl mx-auto">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Attendance Module Settings</h1>
          <p className="text-sm text-muted-foreground">Configure global automated leave credit rules and schedule.</p>
        </div>
        <Button variant="outline" size="sm" onClick={fetchSettings} disabled={loading}>
          <RefreshCw className="h-4 w-4 mr-2" />
          Refresh
        </Button>
      </div>

      <Card className="border shadow-sm">
        <CardHeader>
          <CardTitle>Attendance & Leave Credit Rules</CardTitle>
          <CardDescription>
            Specify the date, credit trigger modes, and monthly credit timings for all employee records.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Date & Time Section */}
            <div className="space-y-4 border-b pb-6">
              <h3 className="text-base font-semibold flex items-center gap-2">
                <Calendar className="h-4 w-4 text-primary" />
                Select Date & Time
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="dateTime">
                    Date & Time <span className="text-destructive">*</span>
                  </Label>
                  <Input
                    id="dateTime"
                    type="text"
                    placeholder="e.g. 10/14/2026 10:00 AM"
                    value={dateTime}
                    onChange={(e) => setDateTime(e.target.value)}
                    required
                  />
                  <p className="text-xs text-muted-foreground">Select the base date and time for attendance settings.</p>
                </div>
              </div>
            </div>

            {/* Leave Credit Settings Section */}
            <div className="space-y-4 border-b pb-6">
              <h3 className="text-base font-semibold flex items-center gap-2">
                <Clock className="h-4 w-4 text-primary" />
                Leave Credit Settings
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="creditMode">
                    Select Credit Mode <span className="text-destructive">*</span>
                  </Label>
                  <Select
                    value={creditMode}
                    onValueChange={(val: any) => setCreditMode(val)}
                  >
                    <SelectTrigger id="creditMode">
                      <SelectValue placeholder="Select Credit Mode" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="last_day_of_every_month">Last Day Of Every Month</SelectItem>
                      <SelectItem value="start_day_of_every_month">Start Day Of Every Month</SelectItem>
                      <SelectItem value="specific_number">Specific Number</SelectItem>
                    </SelectContent>
                  </Select>
                  <p className="text-xs text-muted-foreground">Choose when leave credits should automatically be added.</p>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="creditTime">
                    Select Time <span className="text-destructive">*</span>
                  </Label>
                  <Input
                    id="creditTime"
                    type="text"
                    placeholder="e.g. 10:00 AM"
                    value={creditTime}
                    onChange={(e) => setCreditTime(e.target.value)}
                    required
                  />
                  <p className="text-xs text-muted-foreground">Time when leave credits will be processed.</p>
                </div>
              </div>

              {/* Start Day of Every Month */}
              {creditMode === 'start_day_of_every_month' && (
                <div className="mt-4 p-4 rounded-lg bg-muted/40 space-y-2">
                  <Label htmlFor="startDay">
                    Start Day of Every Month (1-31) <span className="text-destructive">*</span>
                  </Label>
                  <Input
                    id="startDay"
                    type="number"
                    min={1}
                    max={31}
                    placeholder="Enter day (1-31)"
                    value={startDay}
                    onChange={(e) => setStartDay(e.target.value)}
                    className="max-w-xs"
                    required
                  />
                  <p className="text-xs text-muted-foreground">Day of the month when leave credits should be triggered.</p>
                </div>
              )}

              {/* Last Day of Every Month */}
              {creditMode === 'last_day_of_every_month' && (
                <div className="mt-4 p-4 rounded-lg bg-muted/40 space-y-2">
                  <Label htmlFor="lastDay">
                    Last Day of Every Month (1-31) <span className="text-destructive">*</span>
                  </Label>
                  <Input
                    id="lastDay"
                    type="number"
                    min={1}
                    max={31}
                    placeholder="Enter day (1-31)"
                    value={lastDay}
                    onChange={(e) => setLastDay(e.target.value)}
                    className="max-w-xs"
                  />
                  <p className="text-xs text-muted-foreground">Day of the month for end-of-month leave credit process.</p>
                </div>
              )}

              {/* Specific Number Mode Cards */}
              {creditMode === 'specific_number' && (
                <div className="mt-4 space-y-3 p-4 rounded-lg bg-muted/30 border">
                  <Label className="font-medium text-sm">
                    Select Day Number (1-30) <span className="text-destructive">*</span>
                  </Label>
                  <div className="flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2.5 rounded-md border border-blue-200">
                    <Info className="h-4 w-4 shrink-0" />
                    <span>Click on a number to select the day when leave credits should be added. Only one day can be selected.</span>
                  </div>

                  <div className="grid grid-cols-6 sm:grid-cols-10 gap-2 pt-2">
                    {Array.from({ length: 30 }, (_, i) => i + 1).map((num) => {
                      const isSelected = selectedDay === num;
                      return (
                        <button
                          key={num}
                          type="button"
                          onClick={() => setSelectedDay(num)}
                          className={`h-11 flex items-center justify-center rounded-md font-semibold text-sm transition-all border ${
                            isSelected
                              ? 'bg-primary text-primary-foreground border-primary shadow-sm scale-105'
                              : 'bg-background hover:bg-muted text-foreground border-border'
                          }`}
                        >
                          {num}
                        </button>
                      );
                    })}
                  </div>

                  <div className="flex items-center justify-between pt-2">
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={() => setSelectedDay(null)}
                      disabled={!selectedDay}
                    >
                      Clear Selection
                    </Button>
                    <span className="text-xs text-muted-foreground">
                      <strong>Selected Day:</strong> {selectedDay ? `Day ${selectedDay}` : 'None'}
                    </span>
                  </div>
                </div>
              )}
            </div>

            {/* Form Actions */}
            <div className="flex items-center justify-end gap-3 pt-2">
              <Button type="button" variant="outline" onClick={fetchSettings} disabled={saving}>
                Cancel
              </Button>
              <Button type="submit" disabled={saving}>
                {saving ? (
                  <>
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    Saving...
                  </>
                ) : (
                  <>
                    <Save className="mr-2 h-4 w-4" />
                    Save Changes
                  </>
                )}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
