"use client";

import { useState, useEffect } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import { Switch } from "@/components/ui/switch";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Pencil, Trash2, Plus } from "lucide-react";

export default function ShiftPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const [loading, setLoading] = useState(false);
  const [shifts, setShifts] = useState<any[]>([]);
  const [isSheetOpen, setIsSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [step, setStep] = useState(1);

  // Toggles state
  const [shiftEnabled, setShiftEnabled] = useState(false);
  const [restEnabled, setRestEnabled] = useState(false);
  const [shortBreakEnabled, setShortBreakEnabled] = useState(false);
  const [shortBreak2Enabled, setShortBreak2Enabled] = useState(false);
  const [halfDayEnabled, setHalfDayEnabled] = useState(false);
  const [earlyLoginPunchIn, setEarlyLoginPunchIn] = useState("no");

  const initialForm = {
    // Shift Time
    code: "",
    shift_name: "",
    in_time: "",
    out_time: "",
    shift_hours: "",
    
    // Effective Period
    start_from_date: "",
    end_date: "",

    // Rest Time
    lunch_out: "",
    lunch_in: "",
    lunch_hours: "",

    // Short Break 1
    short_break_out: "",
    short_break_in: "",
    short_break_hours: "",

    // Short Break 2
    short_break_out_2: "",
    short_break_in_2: "",
    short_break_hours_2: "",

    // Half Day
    half_day_start_time: "",
    half_day_end_time: "",
    half_day_hours: "",
    
    // Deduction Rules
    absent_below_hours: "",
    absent_below_minutes: "",
    absent_deduction: "",
    half_day_below_hours: "",
    half_day_below_minutes: "",
    half_day_deduction: "",
    short_leave_below_hours: "",
    short_leave_below_minutes: "",
    short_leave_deduction: "",

    // Relaxation
    relaxation_time: "",
    max_relaxations_allowed: "",

    // Overtime
    check_over_time: false,
    overtime_allowed: "",

    // Others
    auto_punchout_time: "",
    max_punch_in_allowed: "",
    punch_calculation_mode: "",

    // Weekly Offs
    weekly_off_1: "",
    weekly_off_2: "",
    weekly_off_3: "",
    weekly_off_4: "",
    saturday_offs: [] as string[],
    saturday_half_days: [] as string[],
    half_day_time: "",

    // Early Login
    early_login_minutes: "",
  };

  const [form, setForm] = useState(initialForm);

  useEffect(() => {
    fetchShifts();
  }, [boxSlug]);

  const fetchShifts = async () => {
    try {
      const res = await apiClient.get(`/${boxSlug}/shifts/list`);
      if (res.data) {
        setShifts(res.data.data || res.data);
      } else if (Array.isArray(res)) {
         setShifts(res);
      }
    } catch (err) {
      console.error(err);
      toast.error("Failed to load shifts");
    }
  };

  const handleEdit = async (shift: any) => {
    try {
      const res = await apiClient.get(`/${boxSlug}/shifts/details?shiftId=${shift.id}`);
      if (res.data) {
        const data = res.data;
        
        // Convert any null values from the database to empty strings to prevent React input warnings
        // And convert ISO date strings to YYYY-MM-DD for date inputs
        const sanitizedData = Object.fromEntries(
          Object.entries(data).map(([key, value]) => {
            if (value === null) return [key, ""];
            if ((key === "start_from_date" || key === "end_date") && typeof value === "string") {
              return [key, value.split('T')[0]];
            }
            return [key, value];
          })
        );
        
        let calculationMode = "";
        if (data.all_in_out_calculation === 1 || data.all_in_out_calculation === true) {
          calculationMode = "all_in_out";
        } else if (data.first_in_last_out_calculation === 1 || data.first_in_last_out_calculation === true) {
          calculationMode = "first_in_last_out";
        }

        setForm({ ...initialForm, ...sanitizedData, punch_calculation_mode: calculationMode });
        setShiftEnabled(data.shift_enabled === 1 || data.shift_enabled === true);
        setRestEnabled(data.rest_enabled === 1 || data.rest_enabled === true);
        setShortBreakEnabled(data.short_break_enabled === 1 || data.short_break_enabled === true);
        setShortBreak2Enabled(data.short_break_enabled_2 === 1 || data.short_break_enabled_2 === true);
        setHalfDayEnabled(data.half_day_enabled === 1 || data.half_day_enabled === true);
        setEarlyLoginPunchIn(data.early_login_punchIn === 1 || data.early_login_punchIn === true || data.early_login_punchIn === "1" ? "yes" : "no");
        setEditingId(shift.id);
        setStep(1);
        setIsSheetOpen(true);
      }
    } catch (err) {
      console.error(err);
      toast.error("Failed to fetch shift details");
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm("Are you sure you want to delete this shift?")) return;
    try {
      const res = await apiClient.delete(`/${boxSlug}/shifts`, { 
        body: JSON.stringify({ shift_id: id }),
        headers: { 'Content-Type': 'application/json' }
      });
      if (res.message || res.success) {
        toast.success("Shift deleted successfully");
        fetchShifts();
      } else {
        toast.error("Failed to delete shift");
      }
    } catch (err) {
      console.error(err);
      toast.error("Something went wrong");
    }
  };

  const openCreate = () => {
    setForm(initialForm);
    setShiftEnabled(false);
    setRestEnabled(false);
    setShortBreakEnabled(false);
    setShortBreak2Enabled(false);
    setHalfDayEnabled(false);
    setEarlyLoginPunchIn("no");
    setEditingId(null);
    setStep(1);
    setIsSheetOpen(true);
  };

  // Helper to calculate hours difference
  const calculateHours = (start: string, end: string) => {
    if (!start || !end) return "";
    const [startH, startM] = start.split(":").map(Number);
    const [endH, endM] = end.split(":").map(Number);
    
    let diffMins = (endH * 60 + endM) - (startH * 60 + startM);
    if (diffMins < 0) {
      // If end time is past midnight
      diffMins += 24 * 60; 
    }
    
    const hrs = Math.floor(diffMins / 60).toString().padStart(2, "0");
    const mins = (diffMins % 60).toString().padStart(2, "0");
    return `${hrs}:${mins}`;
  };

  // Auto-calculate hours when times change
  useEffect(() => {
    setForm(prev => ({
      ...prev,
      shift_hours: calculateHours(prev.in_time, prev.out_time),
      lunch_hours: calculateHours(prev.lunch_out, prev.lunch_in),
      short_break_hours: calculateHours(prev.short_break_out, prev.short_break_in),
      short_break_hours_2: calculateHours(prev.short_break_out_2, prev.short_break_in_2),
      half_day_hours: calculateHours(prev.half_day_start_time, prev.half_day_end_time),
    }));
  }, [
    form.in_time, form.out_time, 
    form.lunch_out, form.lunch_in,
    form.short_break_out, form.short_break_in,
    form.short_break_out_2, form.short_break_in_2,
    form.half_day_start_time, form.half_day_end_time
  ]);

  // Handle Switch Changes - Disables & Clears related fields
  const handleShiftToggle = (checked: boolean) => {
    setShiftEnabled(checked);
    if (!checked) {
      setForm(prev => ({ ...prev, code: "", shift_name: "", in_time: "", out_time: "", shift_hours: "" }));
    }
  };

  const handleRestToggle = (checked: boolean) => {
    setRestEnabled(checked);
    if (!checked) {
      setForm(prev => ({ ...prev, lunch_out: "", lunch_in: "", lunch_hours: "" }));
    }
  };

  const handleShortBreakToggle = (checked: boolean) => {
    setShortBreakEnabled(checked);
    if (!checked) {
      setForm(prev => ({ ...prev, short_break_out: "", short_break_in: "", short_break_hours: "" }));
    }
  };

  const handleShortBreak2Toggle = (checked: boolean) => {
    setShortBreak2Enabled(checked);
    if (!checked) {
      setForm(prev => ({ ...prev, short_break_out_2: "", short_break_in_2: "", short_break_hours_2: "" }));
    }
  };

  const handleHalfDayToggle = (checked: boolean) => {
    setHalfDayEnabled(checked);
    if (!checked) {
      setForm(prev => ({ ...prev, half_day_start_time: "", half_day_end_time: "", half_day_hours: "" }));
    }
  };

  const handleEarlyLoginToggle = (val: string) => {
    setEarlyLoginPunchIn(val);
    if (val === "no") {
      setForm(prev => ({ ...prev, early_login_minutes: "" }));
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setForm(prev => ({ ...prev, [name]: value }));
  };

  const handleOvertimeToggle = (checked: boolean) => {
    setForm(prev => ({ 
      ...prev, 
      check_over_time: checked,
      ...( !checked ? { overtime_allowed: "" } : {} ) 
    }));
  };

  const handleArrayCheckbox = (name: 'saturday_offs' | 'saturday_half_days', value: string, checked: boolean) => {
    setForm(prev => {
      const currentArray = prev[name];
      if (checked) {
        return { ...prev, [name]: [...currentArray, value] };
      } else {
        return { ...prev, [name]: currentArray.filter(item => item !== value) };
      }
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      const payload = {
        ...form,
        shift_enabled: shiftEnabled ? 1 : 0,
        rest_enabled: restEnabled ? 1 : 0,
        short_break_enabled: shortBreakEnabled ? 1 : 0,
        short_break_enabled_2: shortBreak2Enabled ? 1 : 0,
        half_day_enabled: halfDayEnabled ? 1 : 0,
        early_login_punchIn: earlyLoginPunchIn === "yes" ? "1" : "0",
        absent_below_hours: form.absent_below_hours ? parseInt(form.absent_below_hours as string, 10) : null,
        absent_below_minutes: form.absent_below_minutes ? parseInt(form.absent_below_minutes as string, 10) : null,
        half_day_below_hours: form.half_day_below_hours ? parseInt(form.half_day_below_hours as string, 10) : null,
        half_day_below_minutes: form.half_day_below_minutes ? parseInt(form.half_day_below_minutes as string, 10) : null,
        short_leave_below_hours: form.short_leave_below_hours ? parseInt(form.short_leave_below_hours as string, 10) : null,
        short_leave_below_minutes: form.short_leave_below_minutes ? parseInt(form.short_leave_below_minutes as string, 10) : null,
        max_punch_in_allowed: form.max_punch_in_allowed ? parseInt(form.max_punch_in_allowed as string, 10) : null,
        max_relaxations_allowed: form.max_relaxations_allowed ? parseInt(form.max_relaxations_allowed as string, 10) : null,
      };

      let res;
      if (editingId) {
        res = await apiClient.put(`/${boxSlug}/shifts`, { ...payload, shift_id: editingId });
      } else {
        res = await apiClient.post(`/${boxSlug}/shifts`, payload);
      }

      if (res.success || res.status === 200 || res.status === 201 || res.message) {
        toast.success(`Shift ${editingId ? 'updated' : 'saved'} successfully!`);
        setIsSheetOpen(false);
        fetchShifts();
      } else {
        toast.error("Failed to save shift.");
      }
    } catch (err) {
      toast.error("Something went wrong.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-6 max-w-7xl mx-auto space-y-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-slate-800">Shifts</h1>
        <Button onClick={openCreate} className="flex items-center gap-2">
          <Plus className="w-4 h-4" /> Add Shift
        </Button>
      </div>

      <Card>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>#</TableHead>
              <TableHead>Code</TableHead>
              <TableHead>Shift Name</TableHead>
              <TableHead>In Time</TableHead>
              <TableHead>Out Time</TableHead>
              <TableHead>Shift Hours</TableHead>
              <TableHead>Created At</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {shifts && shifts.length > 0 ? (
              shifts.map((shift, i) => (
                <TableRow key={shift.id}>
                  <TableCell>{i + 1}</TableCell>
                  <TableCell className="font-medium">{shift.code}</TableCell>
                  <TableCell>{shift.shift_name || '-'}</TableCell>
                  <TableCell>{shift.in_time}</TableCell>
                  <TableCell>{shift.out_time}</TableCell>
                  <TableCell>{shift.shift_hours}</TableCell>
                  <TableCell>{shift.created_at}</TableCell>
                  <TableCell className="text-right space-x-2">
                    <Button variant="outline" size="icon" onClick={() => handleEdit(shift)}>
                      <Pencil className="w-4 h-4 text-blue-500" />
                    </Button>
                    <Button variant="outline" size="icon" onClick={() => handleDelete(shift.id)}>
                      <Trash2 className="w-4 h-4 text-red-500" />
                    </Button>
                  </TableCell>
                </TableRow>
              ))
            ) : (
              <TableRow>
                <TableCell colSpan={8} className="text-center h-24 text-muted-foreground">
                  No shifts found.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </Card>

      <Dialog open={isSheetOpen} onOpenChange={setIsSheetOpen}>
        <DialogContent className="w-full max-w-[95vw] lg:max-w-[1200px] overflow-y-auto max-h-[95vh]">
          <DialogHeader className="mb-2">
            <DialogTitle>{editingId ? 'Edit Shift' : 'Add Shift Information Entry'}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex space-x-4 border-b pb-2 mb-4 overflow-x-auto">
            {['Basic Details', 'Rules & Allowances', 'Weekly Offs', 'Config'].map((label, idx) => (
              <button
                key={idx}
                type="button"
                onClick={() => setStep(idx + 1)}
                className={`whitespace-nowrap pb-2 px-1 text-sm font-medium border-b-2 transition-colors ${
                  step === idx + 1
                    ? 'border-primary text-primary'
                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                }`}
              >
                {idx + 1}. {label}
              </button>
            ))}
          </div>

          <div className={step === 1 ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4' : 'hidden'}>
        {/* Shift Time Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 py-3">
            <CardTitle className="text-base">Shift Time</CardTitle>
            <div className="flex items-center space-x-2">
              <Label className="text-xs">Disable</Label>
              <Switch checked={shiftEnabled} onCheckedChange={handleShiftToggle} className="scale-75" />
              <Label className="text-xs">Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-2 pb-4">
            <div className="space-y-1">
              <Label className="text-xs">Code</Label>
              <Input name="code" value={form.code} onChange={handleInputChange} disabled={!shiftEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">Shift Name</Label>
              <Input name="shift_name" value={form.shift_name} onChange={handleInputChange} disabled={!shiftEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">In Time</Label>
              <Input name="in_time" type="time" value={form.in_time} onChange={handleInputChange} disabled={!shiftEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">Out Time</Label>
              <Input name="out_time" type="time" value={form.out_time} onChange={handleInputChange} disabled={!shiftEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1 col-span-2">
              <Label className="text-xs">Hrs.</Label>
              <Input name="shift_hours" value={form.shift_hours} readOnly className="bg-slate-50 cursor-not-allowed h-8 text-xs" />
            </div>
          </CardContent>
        </Card>

        {/* Effective Period */}
        <Card>
          <CardHeader>
            <CardTitle>Effective Period</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label>Start From</Label>
              <Input name="start_from_date" type="date" value={form.start_from_date} onChange={handleInputChange} />
            </div>
            <div className="space-y-2">
              <Label>End Date</Label>
              <Input name="end_date" type="date" value={form.end_date} onChange={handleInputChange} />
            </div>
          </CardContent>
        </Card>

        {/* Rest Time Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle>Rest Time</CardTitle>
            <div className="flex items-center space-x-2">
              <Label>Disable</Label>
              <Switch checked={restEnabled} onCheckedChange={handleRestToggle} />
              <Label>Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div className="space-y-2">
              <Label>Lunch Out</Label>
              <Input name="lunch_out" type="time" value={form.lunch_out} onChange={handleInputChange} disabled={!restEnabled} />
            </div>
            <div className="space-y-2">
              <Label>Lunch In</Label>
              <Input name="lunch_in" type="time" value={form.lunch_in} onChange={handleInputChange} disabled={!restEnabled} />
            </div>
            <div className="space-y-2">
              <Label>Hrs.</Label>
              <Input name="lunch_hours" value={form.lunch_hours} onChange={handleInputChange} disabled={!restEnabled} />
            </div>
          </CardContent>
        </Card>

        {/* Short Break 1 Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle>Short Break 1</CardTitle>
            <div className="flex items-center space-x-2">
              <Label>Disable</Label>
              <Switch checked={shortBreakEnabled} onCheckedChange={handleShortBreakToggle} />
              <Label>Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div className="space-y-2">
              <Label>Break Out</Label>
              <Input name="short_break_out" type="time" value={form.short_break_out} onChange={handleInputChange} disabled={!shortBreakEnabled} />
            </div>
            <div className="space-y-2">
              <Label>Break In</Label>
              <Input name="short_break_in" type="time" value={form.short_break_in} onChange={handleInputChange} disabled={!shortBreakEnabled} />
            </div>
            <div className="space-y-2">
              <Label>Hrs.</Label>
              <Input name="short_break_hours" value={form.short_break_hours} onChange={handleInputChange} disabled={!shortBreakEnabled} />
            </div>
          </CardContent>
        </Card>

        {/* Short Break 2 Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle>Short Break 2</CardTitle>
            <div className="flex items-center space-x-2">
              <Label>Disable</Label>
              <Switch checked={shortBreak2Enabled} onCheckedChange={handleShortBreak2Toggle} />
              <Label>Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div className="space-y-2">
              <Label>Break 2 Out</Label>
              <Input name="short_break_out_2" type="time" value={form.short_break_out_2} onChange={handleInputChange} disabled={!shortBreak2Enabled} />
            </div>
            <div className="space-y-2">
              <Label>Break 2 In</Label>
              <Input name="short_break_in_2" type="time" value={form.short_break_in_2} onChange={handleInputChange} disabled={!shortBreak2Enabled} />
            </div>
            <div className="space-y-2">
              <Label>Hrs.</Label>
              <Input name="short_break_hours_2" value={form.short_break_hours_2} onChange={handleInputChange} disabled={!shortBreak2Enabled} />
            </div>
          </CardContent>
        </Card>

        {/* Half Day Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle>Half Day</CardTitle>
            <div className="flex items-center space-x-2">
              <Label>Disable</Label>
              <Switch checked={halfDayEnabled} onCheckedChange={handleHalfDayToggle} />
              <Label>Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div className="space-y-2">
              <Label>Start Time</Label>
              <Input name="half_day_start_time" type="time" value={form.half_day_start_time} onChange={handleInputChange} disabled={!halfDayEnabled} />
            </div>
            <div className="space-y-2">
              <Label>End Time</Label>
              <Input name="half_day_end_time" type="time" value={form.half_day_end_time} onChange={handleInputChange} disabled={!halfDayEnabled} />
            </div>
            <div className="space-y-2">
              <Label>Hrs.</Label>
              <Input name="half_day_hours" value={form.half_day_hours} onChange={handleInputChange} disabled={!halfDayEnabled} />
            </div>
          </CardContent>
        </Card>
        </div>
        
        <div className={step === 2 ? 'grid grid-cols-1 lg:grid-cols-2 gap-4' : 'hidden'}>
        {/* Deduction Rules Section */}
        <Card>
          <CardHeader>
            <CardTitle>Deduction Rules Based on Working Hours</CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Absent Section */}
              <div className="space-y-4">
                <Label className="font-semibold text-slate-700">Mark as Absent if working hours are less than:</Label>
                <div className="flex space-x-4">
                  <div className="flex-1 space-y-1">
                    <Label className="text-xs text-slate-500">Hours</Label>
                    <Input name="absent_below_hours" type="number" min="0" value={form.absent_below_hours} onChange={handleInputChange} placeholder="H" />
                  </div>
                  <div className="flex-1 space-y-1">
                    <Label className="text-xs text-slate-500">Minutes</Label>
                    <Input name="absent_below_minutes" type="number" min="0" max="59" value={form.absent_below_minutes} onChange={handleInputChange} placeholder="M" />
                  </div>
                </div>
              </div>
              <div className="space-y-4 flex flex-col justify-end">
                <Label className="font-semibold text-slate-700">Attendance deduction for Absence (in %):</Label>
                <Input name="absent_deduction" value={form.absent_deduction} onChange={handleInputChange} placeholder="e.g. 1.0 for 1%" />
              </div>

              {/* Half Day Section */}
              <div className="space-y-4">
                <Label className="font-semibold text-slate-700">Mark as Half Day if working hours are less than:</Label>
                <div className="flex space-x-4">
                  <div className="flex-1 space-y-1">
                    <Label className="text-xs text-slate-500">Hours</Label>
                    <Input name="half_day_below_hours" type="number" min="0" value={form.half_day_below_hours} onChange={handleInputChange} placeholder="H" />
                  </div>
                  <div className="flex-1 space-y-1">
                    <Label className="text-xs text-slate-500">Minutes</Label>
                    <Input name="half_day_below_minutes" type="number" min="0" max="59" value={form.half_day_below_minutes} onChange={handleInputChange} placeholder="M" />
                  </div>
                </div>
              </div>
              <div className="space-y-4 flex flex-col justify-end">
                <Label className="font-semibold text-slate-700">Attendance deduction for Half Day (in %):</Label>
                <Input name="half_day_deduction" value={form.half_day_deduction} onChange={handleInputChange} placeholder="e.g. 0.5 for 0.5%" />
              </div>

              {/* Short Leave Section */}
              <div className="space-y-4">
                <Label className="font-semibold text-slate-700">Mark as Short Leave if working hours are less than:</Label>
                <div className="flex space-x-4">
                  <div className="flex-1 space-y-1">
                    <Label className="text-xs text-slate-500">Hours</Label>
                    <Input name="short_leave_below_hours" type="number" min="0" value={form.short_leave_below_hours} onChange={handleInputChange} placeholder="H" />
                  </div>
                  <div className="flex-1 space-y-1">
                    <Label className="text-xs text-slate-500">Minutes</Label>
                    <Input name="short_leave_below_minutes" type="number" min="0" max="59" value={form.short_leave_below_minutes} onChange={handleInputChange} placeholder="M" />
                  </div>
                </div>
              </div>
              <div className="space-y-4 flex flex-col justify-end">
                <Label className="font-semibold text-slate-700">Attendance deduction for Short Leave (in %):</Label>
                <Input name="short_leave_deduction" value={form.short_leave_deduction} onChange={handleInputChange} placeholder="e.g. 0.25 for 0.25%" />
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Relaxation Time */}
        <Card>
          <CardHeader>
            <CardTitle>Relaxation Time</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label>Relaxation Time</Label>
              <Input name="relaxation_time" type="time" value={form.relaxation_time} onChange={handleInputChange} />
            </div>
            <div className="space-y-2">
              <Label>Max Relaxations Allowed</Label>
              <Input name="max_relaxations_allowed" type="number" min="0" max="31" value={form.max_relaxations_allowed} onChange={handleInputChange} placeholder="e.g. 4" />
            </div>
          </CardContent>
        </Card>

        {/* Overtime Allow */}
        <Card>
          <CardHeader>
            <CardTitle>Overtime Allow</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center space-x-2">
              <Switch checked={form.check_over_time} onCheckedChange={handleOvertimeToggle} id="overtime-switch" />
              <Label htmlFor="overtime-switch">Enable Overtime Fields</Label>
            </div>
            <div className="space-y-2 max-w-md">
              <Label>Max Overtime Allowed</Label>
              <Input name="overtime_allowed" type="time" value={form.overtime_allowed} onChange={handleInputChange} disabled={!form.check_over_time} />
            </div>
          </CardContent>
        </Card>

        {/* Punch Configs */}
        <Card className="lg:col-span-2">
          <CardHeader className="py-3">
            <CardTitle className="text-base">Punch Configurations</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-3 gap-6 pb-4">
            <div className="space-y-2">
              <Label className="text-sm font-semibold">Auto Punch Out Time</Label>
              <Label className="text-xs text-slate-500 leading-tight block mb-2">If employee forgets, punch out at:</Label>
              <Input name="auto_punchout_time" type="time" step="1" value={form.auto_punchout_time} onChange={handleInputChange} className="h-8 text-sm" />
            </div>
            <div className="space-y-2">
              <Label className="text-sm font-semibold">Max Punch In</Label>
              <Label className="text-xs text-slate-500 leading-tight block mb-2">Allowed punch-ins per day:</Label>
              <Input name="max_punch_in_allowed" type="number" min="1" max="100" value={form.max_punch_in_allowed} onChange={handleInputChange} className="h-8 text-sm" />
            </div>
            <div className="space-y-2">
              <Label className="text-sm font-semibold">Calculation Mode <span className="text-red-500">*</span></Label>
              <RadioGroup value={form.punch_calculation_mode} onValueChange={(val) => setForm(p => ({ ...p, punch_calculation_mode: val }))} className="space-y-1 mt-2">
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="all_in_out" id="calc-all" className="scale-75" />
                  <Label htmlFor="calc-all" className="text-xs">All In/Out (Last as Out)</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="first_in_last_out" id="calc-first-last" className="scale-75" />
                  <Label htmlFor="calc-first-last" className="text-xs">First In / Last Out</Label>
                </div>
              </RadioGroup>
            </div>
          </CardContent>
        </Card>
        </div>

        <div className={step === 3 ? 'grid grid-cols-1 gap-4' : 'hidden'}>
        {/* Weekly Offs */}
        <Card>
          <CardHeader>
            <CardTitle>Weekly Off</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {[1, 2, 3, 4].map(num => {
              const currentValue = (form as any)[`weekly_off_${num}`];
              const selectedElsewhere = [1, 2, 3, 4]
                .filter(n => n !== num)
                .map(n => (form as any)[`weekly_off_${n}`]);

              return (
                <div key={num} className="space-y-2">
                  <Label>{num === 1 ? "1st" : num === 2 ? "2nd" : num === 3 ? "3rd" : "4th"} Weekly Off</Label>
                  <select
                    name={`weekly_off_${num}`}
                    value={currentValue}
                    onChange={handleInputChange}
                    className="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <option value="" disabled>-- Select Day --</option>
                    {["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"].map(d => (
                      <option key={d} value={d} disabled={selectedElsewhere.includes(d)}>{d}</option>
                    ))}
                  </select>
                </div>
              );
            })}
          </CardContent>
        </Card>

        {/* Saturday Specifics */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">
                {form.weekly_off_3 ? `3rd - ${form.weekly_off_3}` : "Saturday Offs"}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex flex-wrap gap-4">
                {["1st", "2nd", "3rd", "4th", "5th"].map(val => {
                  const isDisabled = form.saturday_half_days.includes(val);
                  return (
                    <label key={val} className={`flex items-center space-x-2 ${isDisabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}`}>
                      <input type="checkbox" className="form-checkbox h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        checked={form.saturday_offs.includes(val)}
                        disabled={isDisabled}
                        onChange={(e) => handleArrayCheckbox('saturday_offs', val, e.target.checked)}
                      />
                      <span className="text-sm">{val}</span>
                    </label>
                  );
                })}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">
                {form.weekly_off_4 ? `4th Half - ${form.weekly_off_4}` : "Saturday Half Day"}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex flex-wrap gap-4">
                {["1st", "2nd", "3rd", "4th", "5th"].map(val => {
                  const isDisabled = form.saturday_offs.includes(val);
                  return (
                    <label key={val} className={`flex items-center space-x-2 ${isDisabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}`}>
                      <input type="checkbox" className="form-checkbox h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        checked={form.saturday_half_days.includes(val)}
                        disabled={isDisabled}
                        onChange={(e) => handleArrayCheckbox('saturday_half_days', val, e.target.checked)}
                      />
                      <span className="text-sm">{val}</span>
                    </label>
                  );
                })}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Half Day From</CardTitle>
            </CardHeader>
            <CardContent>
              <RadioGroup value={form.half_day_time} onValueChange={(val) => setForm(p => ({ ...p, half_day_time: val }))} className="flex space-x-4">
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="1st half" id="half-1st" />
                  <Label htmlFor="half-1st">1st half</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="2nd half" id="half-2nd" />
                  <Label htmlFor="half-2nd">2nd half</Label>
                </div>
              </RadioGroup>
            </CardContent>
          </Card>
        </div>
        </div>

        <div className={step === 4 ? 'grid grid-cols-1 lg:grid-cols-2 gap-4' : 'hidden'}>
        {/* Early Login section that uses show/hide mechanism */}
        <Card>
          <CardHeader>
            <CardTitle>Early Login Punch In Allowed</CardTitle>
          </CardHeader>
          <CardContent>
            <RadioGroup value={earlyLoginPunchIn} onValueChange={handleEarlyLoginToggle} className="flex space-x-4">
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="yes" id="early-yes" />
                <Label htmlFor="early-yes">Yes</Label>
              </div>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="no" id="early-no" />
                <Label htmlFor="early-no">No</Label>
              </div>
            </RadioGroup>

            {/* This div represents the slideDown / slideUp logic. 
                In React, we conditionally render it (show/hide). */}
            {earlyLoginPunchIn === "yes" && (
              <div className="mt-4 w-1/2">
                <Label>If Yes, enter minutes allowed</Label>
                <Input 
                  name="early_login_minutes" 
                  value={form.early_login_minutes} 
                  onChange={handleInputChange} 
                  placeholder="Enter minutes" 
                  className="mt-2"
                />
                <p className="text-sm text-slate-500 mt-1">Enter how many minutes early punch-in is allowed.</p>
              </div>
            )}
          </CardContent>
        </Card>
        </div>

        <div className="flex justify-between space-x-4 pt-4 border-t mt-4">
          <Button type="button" variant="outline" onClick={() => setIsSheetOpen(false)}>Cancel</Button>
          <div className="flex space-x-2">
            {step > 1 && (
              <Button type="button" variant="secondary" onClick={() => setStep(p => p - 1)}>
                Previous
              </Button>
            )}
            {step < 4 ? (
              <Button type="button" onClick={() => setStep(p => p + 1)}>
                Next
              </Button>
            ) : (
              <Button type="submit" disabled={loading}>
                {loading ? "Saving..." : (editingId ? "Update Shift" : "Save Shift")}
              </Button>
            )}
          </div>
        </div>
      </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
