"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  CalendarDays,
  Plus,
  RefreshCw,
  Search,
  Pencil,
  Trash2,
  CheckCircle2,
  XCircle,
  Tag,
  Clock,
  Layers,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
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

export default function HolidayTypePage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const [loading, setLoading] = useState(true);
  const [types, setTypes] = useState<any[]>([]);
  const [searchTerm, setSearchTerm] = useState("");

  // Modals state
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const initialForm = {
    holiday_type: "",
    holiday_code: "",
    is_mandatory: "1",
    max_limit: "0",
    calculate_in_calendar: "No",
    status: "1",
  };
  const [formData, setFormData] = useState(initialForm);

  const fetchHolidayTypes = async () => {
    setLoading(true);
    try {
      const res: any = await apiClient.get(`/${boxSlug}/holidays/types`);
      if (res?.data && Array.isArray(res.data)) {
        setTypes(res.data);
      } else if (Array.isArray(res)) {
        setTypes(res);
      } else {
        setTypes([]);
      }
    } catch (err: any) {
      console.error("Failed to load holiday types", err);
      toast.error(err.message || "Failed to load holiday types");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchHolidayTypes();
    }
  }, [boxSlug]);

  const handleOpenAdd = () => {
    setEditingId(null);
    setFormData(initialForm);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (item: any) => {
    setEditingId(item.id);
    setFormData({
      holiday_type: item.holiday_type || "",
      holiday_code: item.holiday_code || "",
      is_mandatory: String(item.is_mandatory ?? "1"),
      max_limit: String(item.max_limit ?? "0"),
      calculate_in_calendar: item.calculate_in_calendar || "No",
      status: String(item.status ?? "1"),
    });
    setIsModalOpen(true);
  };

  const handleSaveHolidayType = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.holiday_type.trim()) {
      toast.error("Please enter holiday type name");
      return;
    }
    if (!formData.holiday_code.trim()) {
      toast.error("Please enter holiday code");
      return;
    }

    setSubmitting(true);
    try {
      const payload: any = {
        holiday_type: formData.holiday_type,
        holiday_code: formData.holiday_code,
        is_mandatory: formData.is_mandatory,
        max_limit: Number(formData.max_limit),
        calculate_in_calendar: formData.calculate_in_calendar,
        status: formData.status,
      };

      if (editingId) {
        payload.id = editingId;
        await apiClient.put(`/${boxSlug}/holidays/types`, payload);
        toast.success("Holiday type updated successfully");
      } else {
        await apiClient.post(`/${boxSlug}/holidays/types`, payload);
        toast.success("Holiday type created successfully");
      }
      setIsModalOpen(false);
      fetchHolidayTypes();
    } catch (err: any) {
      toast.error(err.message || "Failed to save holiday type");
    } finally {
      setSubmitting(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this holiday type?")) return;
    try {
      await apiClient.delete(`/${boxSlug}/holidays/types`, {
        params: { id },
      });
      toast.success("Holiday type deleted");
      fetchHolidayTypes();
    } catch (err: any) {
      toast.error(err.message || "Failed to delete holiday type");
    }
  };

  const filteredTypes = useMemo(() => {
    return types.filter((t) => {
      const matchSearch =
        !searchTerm ||
        t.holiday_type?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        t.holiday_code?.toLowerCase().includes(searchTerm.toLowerCase());
      return matchSearch;
    });
  }, [types, searchTerm]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Holiday Types
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Define categories for holidays, mandatory rules, and calendar calculation limits.
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Button
            variant="outline"
            size="sm"
            onClick={fetchHolidayTypes}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>

          <Button onClick={handleOpenAdd} className="gap-2">
            <Plus className="h-4 w-4" />
            Add Holiday Type
          </Button>
        </div>
      </div>

      {/* Search Toolbar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search holiday types by name or code..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>
          <span className="text-xs text-muted-foreground">
            Total {filteredTypes.length} configured types
          </span>
        </CardContent>
      </Card>

      {/* Table */}
      <Card className="border-border/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader className="bg-muted/40">
              <TableRow>
                <TableHead className="w-[220px]">Holiday Type</TableHead>
                <TableHead>Code</TableHead>
                <TableHead>Mandatory</TableHead>
                <TableHead className="text-right">Max Yearly Limit</TableHead>
                <TableHead>Calendar Calculation</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-12 text-muted-foreground">
                    <div className="flex items-center justify-center gap-2">
                      <RefreshCw className="h-4 w-4 animate-spin" />
                      Loading holiday types...
                    </div>
                  </TableCell>
                </TableRow>
              ) : filteredTypes.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-12 text-muted-foreground">
                    No holiday types configured yet.
                  </TableCell>
                </TableRow>
              ) : (
                filteredTypes.map((item) => {
                  const isMandatory =
                    item.is_mandatory === "1" ||
                    item.is_mandatory === 1 ||
                    item.is_mandatory === true;
                  const isActive = item.status === "1" || item.status === 1 || item.status === "Active";

                  return (
                    <TableRow key={item.id} className="hover:bg-muted/30">
                      <TableCell className="font-semibold text-sm">
                        <div className="flex items-center gap-2">
                          <Tag className="h-4 w-4 text-primary" />
                          <span>{item.holiday_type}</span>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline" className="font-mono text-xs">
                          {item.holiday_code || "-"}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        {isMandatory ? (
                          <Badge className="bg-blue-500/10 text-blue-600 border-blue-200 text-xs">
                            Yes (Mandatory)
                          </Badge>
                        ) : (
                          <Badge variant="secondary" className="text-xs">
                            Optional / Restricted
                          </Badge>
                        )}
                      </TableCell>
                      <TableCell className="text-right font-mono text-sm">
                        {item.max_limit ? `${item.max_limit} Days` : "Unlimited"}
                      </TableCell>
                      <TableCell className="text-sm text-muted-foreground">
                        {item.calculate_in_calendar === "Yes"
                          ? "Yes (Calculated)"
                          : "No"}
                      </TableCell>
                      <TableCell>
                        {isActive ? (
                          <Badge className="bg-emerald-500/10 text-emerald-600 border-emerald-200 text-xs">
                            Active
                          </Badge>
                        ) : (
                          <Badge variant="outline" className="text-xs text-muted-foreground">
                            Inactive
                          </Badge>
                        )}
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex items-center justify-end gap-1">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleOpenEdit(item)}
                            className="h-8 w-8 p-0"
                          >
                            <Pencil className="h-4 w-4 text-muted-foreground" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleDelete(item.id)}
                            className="h-8 w-8 p-0 text-destructive hover:bg-destructive/10"
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  );
                })
              )}
            </TableBody>
          </Table>
        </div>
      </Card>

      {/* Add / Edit Holiday Type Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle className="text-lg font-semibold flex items-center gap-2">
              <CalendarDays className="h-5 w-5 text-primary" />
              {editingId ? "Edit Holiday Type" : "Add Holiday Type"}
            </DialogTitle>
          </DialogHeader>

          <form onSubmit={handleSaveHolidayType} className="space-y-4 pt-2">
            {/* Row 1: Type & Code */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="holiday_type_name">
                  Holiday Type <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="holiday_type_name"
                  placeholder="e.g. National Holiday"
                  value={formData.holiday_type}
                  onChange={(e) =>
                    setFormData((p) => ({ ...p, holiday_type: e.target.value }))
                  }
                  required
                />
              </div>

              <div className="space-y-1.5">
                <Label htmlFor="holiday_type_code">
                  Holiday Code <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="holiday_type_code"
                  placeholder="e.g. NH, RH"
                  value={formData.holiday_code}
                  onChange={(e) =>
                    setFormData((p) => ({ ...p, holiday_code: e.target.value }))
                  }
                  required
                />
              </div>
            </div>

            {/* Row 2: Limit & Status */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="max_limit">Maximum Yearly Limit</Label>
                <Input
                  id="max_limit"
                  type="number"
                  min="0"
                  placeholder="0 for unlimited"
                  value={formData.max_limit}
                  onChange={(e) =>
                    setFormData((p) => ({ ...p, max_limit: e.target.value }))
                  }
                  className="w-full"
                />
              </div>

              <div className="space-y-1.5">
                <Label>Status</Label>
                <Select
                  value={formData.status}
                  onValueChange={(val) =>
                    setFormData((p) => ({ ...p, status: val || "1" }))
                  }
                >
                  <SelectTrigger id="status_select" className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="1">Active</SelectItem>
                    <SelectItem value="0">Inactive</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            {/* Row 3: Mandatory & Calendar Calc options */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
              <div className="space-y-1.5 rounded-lg border p-3 bg-muted/20">
                <Label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground block mb-2">
                  Is Mandatory? <span className="text-destructive">*</span>
                </Label>
                <div className="flex items-center gap-4">
                  <label className="flex items-center gap-2 text-sm font-normal cursor-pointer">
                    <input
                      type="radio"
                      name="is_mandatory"
                      value="1"
                      checked={formData.is_mandatory === "1"}
                      onChange={(e) => setFormData((p) => ({ ...p, is_mandatory: e.target.value }))}
                      className="text-primary focus:ring-primary h-4 w-4"
                    />
                    <span>Yes</span>
                  </label>
                  <label className="flex items-center gap-2 text-sm font-normal cursor-pointer">
                    <input
                      type="radio"
                      name="is_mandatory"
                      value="0"
                      checked={formData.is_mandatory === "0"}
                      onChange={(e) => setFormData((p) => ({ ...p, is_mandatory: e.target.value }))}
                      className="text-primary focus:ring-primary h-4 w-4"
                    />
                    <span>No</span>
                  </label>
                </div>
              </div>

              <div className="space-y-1.5 rounded-lg border p-3 bg-muted/20">
                <Label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground block mb-2">
                  Calculate In Calendar <span className="text-destructive">*</span>
                </Label>
                <div className="flex items-center gap-4">
                  <label className="flex items-center gap-2 text-sm font-normal cursor-pointer">
                    <input
                      type="radio"
                      name="calculate_in_calendar"
                      value="Yes"
                      checked={formData.calculate_in_calendar === "Yes"}
                      onChange={(e) => setFormData((p) => ({ ...p, calculate_in_calendar: e.target.value }))}
                      className="text-primary focus:ring-primary h-4 w-4"
                    />
                    <span>Yes</span>
                  </label>
                  <label className="flex items-center gap-2 text-sm font-normal cursor-pointer">
                    <input
                      type="radio"
                      name="calculate_in_calendar"
                      value="No"
                      checked={formData.calculate_in_calendar === "No"}
                      onChange={(e) => setFormData((p) => ({ ...p, calculate_in_calendar: e.target.value }))}
                      className="text-primary focus:ring-primary h-4 w-4"
                    />
                    <span>No</span>
                  </label>
                </div>
              </div>
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
                {submitting ? "Saving..." : editingId ? "Update Type" : "Create Type"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
