"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  Calendar as CalendarIcon,
  Plus,
  RefreshCw,
  Search,
  Pencil,
  Trash2,
  Globe,
  MapPin,
  Sparkles,
  CheckCircle2,
  Clock,
  FileText,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog";

export default function HolidayPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const currentYear = new Date().getFullYear();
  const [selectedYear, setSelectedYear] = useState(String(currentYear));
  const [availableYears, setAvailableYears] = useState<number[]>([
    currentYear - 1,
    currentYear,
    currentYear + 1,
  ]);
  const [loading, setLoading] = useState(true);
  const [holidays, setHolidays] = useState<any[]>([]);
  const [regions, setRegions] = useState<any[]>([]);
  const [holidayTypeCodes, setHolidayTypeCodes] = useState<any[]>([]);
  const [searchTerm, setSearchTerm] = useState("");

  // Modals state
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const initialForm = {
    title: "",
    date: new Date().toISOString().split("T")[0],
    holiday_type_id: "",
    holiday_type: "",
    holiday_code: "",
    country_region: [] as number[],
    description: "",
    status: "Active",
  };
  const [formData, setFormData] = useState(initialForm);

  const fetchRegions = async () => {
    try {
      const res: any = await apiClient.get(`/${boxSlug}/holidays/regions`);
      if (res?.data && Array.isArray(res.data)) {
        setRegions(res.data);
      }
    } catch (err: any) {
      console.error("Failed to load regions", err);
    }
  };

  const fetchHolidayTypePrefill = async () => {
    try {
      const res: any = await apiClient.get(`/${boxSlug}/holidays/types/prefill`);
      if (res?.codes && Array.isArray(res.codes)) {
        setHolidayTypeCodes(res.codes);
      }
    } catch (err: any) {
      console.error("Failed to load holiday type prefill", err);
    }
  };

  const fetchHolidays = async () => {
    setLoading(true);
    try {
      const res: any = await apiClient.get(
        `/${boxSlug}/holidays?year=${selectedYear}`
      );
      if (res?.holidays && Array.isArray(res.holidays)) {
        setHolidays(res.holidays);
      } else if (Array.isArray(res)) {
        setHolidays(res);
      } else {
        setHolidays([]);
      }

      if (res?.years && Array.isArray(res.years) && res.years.length > 0) {
        setAvailableYears(res.years);
      }
    } catch (err: any) {
      console.error("Failed to load holidays", err);
      toast.error(err.message || "Failed to load holidays");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchRegions();
      fetchHolidayTypePrefill();
      fetchHolidays();
    }
  }, [boxSlug, selectedYear]);

  const handleOpenAdd = () => {
    setEditingId(null);
    setFormData(initialForm);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (holiday: any) => {
    setEditingId(holiday.id);
    let regionIds: number[] = [];
    if (Array.isArray(holiday.country_region)) {
      regionIds = holiday.country_region.map(Number);
    } else if (typeof holiday.country_region === "string") {
      try {
        regionIds = JSON.parse(holiday.country_region);
      } catch (e) {
        regionIds = [];
      }
    }

    // Auto-resolve holiday_type_id
    let matchedTypeId = holiday.holiday_type_id ? String(holiday.holiday_type_id) : "";
    let matchedType = holiday.holiday_type || "";
    let matchedCode = holiday.holiday_code || "";

    const foundType = holidayTypeCodes.find(
      (c) =>
        (matchedTypeId && String(c.id) === matchedTypeId) ||
        (matchedType && (
          String(c.label || "").toLowerCase() === matchedType.toLowerCase() ||
          String(c.value || "").toLowerCase() === matchedType.toLowerCase()
        )) ||
        (matchedCode && (
          String(c.holiday_code || "").toLowerCase() === matchedCode.toLowerCase() ||
          String(c.label || "").toLowerCase() === matchedCode.toLowerCase() ||
          String(c.value || "").toLowerCase() === matchedCode.toLowerCase()
        ))
    );

    if (foundType) {
      matchedTypeId = String(foundType.id);
      if (!matchedType) matchedType = foundType.label || foundType.value || "";
      if (!matchedCode) matchedCode = foundType.holiday_code || "";
    }

    setFormData({
      title: holiday.title || "",
      date: holiday.date || new Date().toISOString().split("T")[0],
      holiday_type_id: matchedTypeId,
      holiday_type: matchedType,
      holiday_code: matchedCode,
      country_region: regionIds,
      description: holiday.description || "",
      status: holiday.status || "Active",
    });
    setIsModalOpen(true);
  };

  useEffect(() => {
    if (isModalOpen && !formData.holiday_type_id && holidayTypeCodes.length > 0) {
      const foundType = holidayTypeCodes.find(
        (c) =>
          (formData.holiday_type && (
            String(c.label || "").toLowerCase() === formData.holiday_type.toLowerCase() ||
            String(c.value || "").toLowerCase() === formData.holiday_type.toLowerCase()
          )) ||
          (formData.holiday_code && (
            String(c.holiday_code || "").toLowerCase() === formData.holiday_code.toLowerCase() ||
            String(c.label || "").toLowerCase() === formData.holiday_code.toLowerCase() ||
            String(c.value || "").toLowerCase() === formData.holiday_code.toLowerCase()
          ))
      );
      if (foundType) {
        setFormData((prev) => ({
          ...prev,
          holiday_type_id: String(foundType.id),
          holiday_type: prev.holiday_type || foundType.label || foundType.value || "",
          holiday_code: prev.holiday_code || foundType.holiday_code || "",
        }));
      }
    }
  }, [holidayTypeCodes, isModalOpen]);

  const handleSelectHolidayType = (typeId: string) => {
    const found = holidayTypeCodes.find((c) => String(c.id) === typeId);
    setFormData((prev) => ({
      ...prev,
      holiday_type_id: typeId,
      holiday_type: found?.label || found?.value || prev.holiday_type || "",
      holiday_code: found?.holiday_code || prev.holiday_code || "",
    }));
  };

  const handleSaveHoliday = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.title.trim()) {
      toast.error("Please enter a holiday title");
      return;
    }
    if (!formData.date) {
      toast.error("Please select a date");
      return;
    }

    setSubmitting(true);
    try {
      const selectedType = holidayTypeCodes.find((c) => String(c.id) === String(formData.holiday_type_id));
      const holidayTypeName = selectedType?.label || selectedType?.value || formData.holiday_type;
      const holidayCode = formData.holiday_code || selectedType?.holiday_code || "";

      const payload: any = {
        date: formData.date,
        holiday_type_id: formData.holiday_type_id ? Number(formData.holiday_type_id) : null,
        holiday_type: holidayTypeName,
        holiday_code: holidayCode,
        country_region: formData.country_region,
        description: formData.description,
        status: formData.status,
      };

      if (editingId) {
        payload.id = editingId;
        payload.title = formData.title;
        payload.titles = formData.title;
        await apiClient.put(`/${boxSlug}/holidays`, payload);
        toast.success("Holiday updated successfully");
      } else {
        payload.title = formData.title;
        payload.titles = formData.title;
        await apiClient.post(`/${boxSlug}/holidays`, payload);
        toast.success("Holiday created successfully");
      }
      setIsModalOpen(false);
      fetchHolidays();
    } catch (err: any) {
      toast.error(err.message || "Failed to save holiday");
    } finally {
      setSubmitting(false);
    }
  };

  const handleDeleteHoliday = async (id: number) => {
    if (!confirm("Are you sure you want to delete this holiday?")) return;
    try {
      const res: any = await apiClient.delete(`/${boxSlug}/holidays`, {
        params: { id, force: true },
      });
      if (res?.status === "warning") {
        if (confirm(`${res.message} Do you want to force delete it?`)) {
          await apiClient.delete(`/${boxSlug}/holidays`, {
            params: { id, force: true },
          });
          toast.success("Holiday force deleted");
          fetchHolidays();
        }
      } else {
        toast.success("Holiday deleted successfully");
        fetchHolidays();
      }
    } catch (err: any) {
      toast.error(err.message || "Failed to delete holiday");
    }
  };

  const filteredHolidays = useMemo(() => {
    return holidays.filter((h) => {
      const matchSearch =
        !searchTerm ||
        h.title?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        h.holiday_type?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        h.description?.toLowerCase().includes(searchTerm.toLowerCase());
      return matchSearch;
    });
  }, [holidays, searchTerm]);

  // Group holidays by month
  const groupedByMonth = useMemo(() => {
    const groups: Record<string, any[]> = {};
    const monthNames = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];

    filteredHolidays.forEach((h) => {
      const date = new Date(h.date);
      const monthName = !isNaN(date.getTime())
        ? monthNames[date.getMonth()]
        : "Unscheduled";
      if (!groups[monthName]) groups[monthName] = [];
      groups[monthName].push(h);
    });

    return groups;
  }, [filteredHolidays]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Holiday Calendar
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Manage official holidays, festive breaks, and regional calendar observances.
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Select
            value={selectedYear}
            onValueChange={(val) => setSelectedYear(val || String(currentYear))}
          >
            <SelectTrigger className="w-[120px] h-9">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              {availableYears.map((y) => (
                <SelectItem key={y} value={String(y)}>
                  {y}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>

          <Button
            variant="outline"
            size="sm"
            onClick={fetchHolidays}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>

          <Button onClick={handleOpenAdd} className="gap-2">
            <Plus className="h-4 w-4" />
            Add Holiday
          </Button>
        </div>
      </div>

      {/* Search Toolbar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search holidays by title, type, description..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>
          <span className="text-xs text-muted-foreground">
            Total {filteredHolidays.length} holidays in {selectedYear}
          </span>
        </CardContent>
      </Card>

      {/* Holidays Grid / List */}
      {loading ? (
        <Card className="p-12 text-center text-muted-foreground">
          <div className="flex items-center justify-center gap-2">
            <RefreshCw className="h-4 w-4 animate-spin" />
            Loading holidays for {selectedYear}...
          </div>
        </Card>
      ) : Object.keys(groupedByMonth).length === 0 ? (
        <Card className="p-12 text-center text-muted-foreground">
          No holidays scheduled for {selectedYear}. Click "Add Holiday" to create one.
        </Card>
      ) : (
        <div className="space-y-6">
          {Object.entries(groupedByMonth).map(([month, monthHolidays]) => (
            <div key={month} className="space-y-3">
              <div className="flex items-center gap-2 border-b border-border/40 pb-1.5">
                <CalendarIcon className="h-4 w-4 text-primary" />
                <h3 className="font-semibold text-sm tracking-wide text-foreground uppercase">
                  {month} ({monthHolidays.length})
                </h3>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {monthHolidays.map((holiday) => {
                  const hDate = new Date(holiday.date);
                  const dayNum = !isNaN(hDate.getTime()) ? hDate.getDate() : "--";
                  const dayName = !isNaN(hDate.getTime())
                    ? hDate.toLocaleDateString("en-US", { weekday: "short" })
                    : "";
                  const isActive = holiday.status === "Active" || holiday.status === "1";
                  const countriesList = Array.isArray(holiday.countries)
                    ? holiday.countries
                    : typeof holiday.countries === "object" && holiday.countries
                    ? Object.values(holiday.countries)
                    : [];

                  return (
                    <Card
                      key={holiday.id}
                      className="border-border/60 hover:shadow-md transition-shadow duration-200 overflow-hidden group"
                    >
                      <CardContent className="p-4">
                        <div className="flex items-start justify-between gap-3">
                          <div className="flex items-center gap-3">
                            <div className="h-14 w-14 rounded-xl bg-primary/10 border border-primary/20 flex flex-col items-center justify-center text-primary shrink-0">
                              <span className="text-xs uppercase font-semibold leading-none text-muted-foreground">
                                {dayName}
                              </span>
                              <span className="text-xl font-bold leading-tight mt-0.5">
                                {dayNum}
                              </span>
                            </div>
                            <div className="min-w-0">
                              <h4 className="font-semibold text-sm text-foreground truncate group-hover:text-primary transition-colors">
                                {holiday.title}
                              </h4>
                              <div className="flex items-center gap-1.5 mt-1">
                                <Badge variant="secondary" className="text-[10px] py-0 px-1.5 font-normal">
                                  {holiday.holiday_type || "General"}
                                </Badge>
                                {isActive ? (
                                  <Badge className="bg-emerald-500/10 text-emerald-600 border-emerald-200 text-[10px] py-0 px-1.5">
                                    Active
                                  </Badge>
                                ) : (
                                  <Badge variant="outline" className="text-[10px] py-0 px-1.5 text-muted-foreground">
                                    Inactive
                                  </Badge>
                                )}
                              </div>
                            </div>
                          </div>

                          <div className="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => handleOpenEdit(holiday)}
                              className="h-7 w-7 p-0"
                            >
                              <Pencil className="h-3.5 w-3.5 text-muted-foreground" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => handleDeleteHoliday(holiday.id)}
                              className="h-7 w-7 p-0 text-destructive hover:bg-destructive/10"
                            >
                              <Trash2 className="h-3.5 w-3.5" />
                            </Button>
                          </div>
                        </div>

                        {holiday.description && (
                          <p className="mt-3 text-xs text-muted-foreground line-clamp-2">
                            {holiday.description}
                          </p>
                        )}

                        {countriesList.length > 0 && (
                          <div className="mt-3 pt-2.5 border-t border-border/40 flex items-center gap-1.5 flex-wrap">
                            <Globe className="h-3 w-3 text-muted-foreground shrink-0" />
                            {countriesList.map((cName: any, idx: number) => (
                              <span
                                key={idx}
                                className="text-[11px] text-muted-foreground bg-muted/60 px-1.5 py-0.5 rounded"
                              >
                                {String(cName)}
                              </span>
                            ))}
                          </div>
                        )}
                      </CardContent>
                    </Card>
                  );
                })}
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Add / Edit Holiday Dialog */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle className="text-lg font-semibold flex items-center gap-2">
              <CalendarIcon className="h-5 w-5 text-primary" />
              {editingId ? "Edit Holiday" : "Add Holiday"}
            </DialogTitle>
          </DialogHeader>

          <form onSubmit={handleSaveHoliday} className="space-y-4 pt-2">
            <div className="space-y-1.5">
              <Label htmlFor="holiday_title" className="text-sm font-medium">
                Holiday Title <span className="text-destructive">*</span>
              </Label>
              <Input
                id="holiday_title"
                placeholder="e.g. Independence Day, Diwali, Christmas"
                value={formData.title}
                onChange={(e) => setFormData((p) => ({ ...p, title: e.target.value }))}
                required
                className="w-full"
              />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="holiday_date" className="text-sm font-medium">
                  Date <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="holiday_date"
                  type="date"
                  value={formData.date}
                  onChange={(e) => setFormData((p) => ({ ...p, date: e.target.value }))}
                  required
                  className="w-full"
                />
              </div>

              <div className="space-y-1.5">
                <Label className="text-sm font-medium">Holiday Type</Label>
                <Select
                  value={formData.holiday_type_id ? String(formData.holiday_type_id) : ""}
                  onValueChange={(val) => handleSelectHolidayType(val || "")}
                >
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Select Type" />
                  </SelectTrigger>
                  <SelectContent>
                    {holidayTypeCodes.map((c) => (
                      <SelectItem key={c.id} value={String(c.id)}>
                        {c.label || c.value}
                      </SelectItem>
                    ))}
                    {formData.holiday_type_id &&
                      !holidayTypeCodes.some((c) => String(c.id) === String(formData.holiday_type_id)) && (
                        <SelectItem value={String(formData.holiday_type_id)}>
                          {formData.holiday_type || `Type #${formData.holiday_type_id}`}
                        </SelectItem>
                      )}
                  </SelectContent>
                </Select>
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="holiday_code_input" className="text-sm font-medium">Holiday Code</Label>
                <Input
                  id="holiday_code_input"
                  placeholder="e.g. IND, NAT"
                  value={formData.holiday_code}
                  onChange={(e) =>
                    setFormData((p) => ({ ...p, holiday_code: e.target.value }))
                  }
                  className="w-full"
                />
              </div>

              <div className="space-y-1.5">
                <Label className="text-sm font-medium">Status</Label>
                <Select
                  value={formData.status}
                  onValueChange={(val) => setFormData((p) => ({ ...p, status: val || "Active" }))}
                >
                  <SelectTrigger className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Active">Active</SelectItem>
                    <SelectItem value="Inactive">Inactive</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <div className="space-y-1.5">
              <Label className="text-sm font-medium">Regional Applicable Countries</Label>
              <Select
                value={formData.country_region[0] ? String(formData.country_region[0]) : "all"}
                onValueChange={(val) => {
                  if (val === "all") {
                    setFormData((p) => ({ ...p, country_region: [] }));
                  } else {
                    setFormData((p) => ({ ...p, country_region: [Number(val)] }));
                  }
                }}
              >
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="All Regions (Global)" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Global / All Regions</SelectItem>
                  {regions.map((r) => (
                    <SelectItem key={r.id} value={String(r.id)}>
                      {r.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="holiday_desc" className="text-sm font-medium">Description / Notes</Label>
              <Textarea
                id="holiday_desc"
                placeholder="Optional notes regarding this holiday..."
                rows={2}
                value={formData.description}
                onChange={(e) => setFormData((p) => ({ ...p, description: e.target.value }))}
                className="w-full resize-none"
              />
            </div>

            <DialogFooter className="pt-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => setIsModalOpen(false)}
                disabled={submitting}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving..." : editingId ? "Update Holiday" : "Create Holiday"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
