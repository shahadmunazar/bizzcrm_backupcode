"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  Calendar as CalendarIcon,
  RefreshCw,
  Search,
  Calculator,
  Download,
  Users,
  Coins,
  Building,
  CheckCircle2,
  FileSpreadsheet,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
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
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function LeaveTransactionAllPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const currentYear = new Date().getFullYear();
  const [selectedYear, setSelectedYear] = useState(String(currentYear));
  const [selectedMonth, setSelectedMonth] = useState("");
  const [loading, setLoading] = useState(true);
  const [transactions, setTransactions] = useState<any[]>([]);
  const [summaryData, setSummaryData] = useState<any[]>([]);
  const [searchTerm, setSearchTerm] = useState("");

  const months = [
    { value: "", label: "All Months" },
    { value: "1", label: "January" },
    { value: "2", label: "February" },
    { value: "3", label: "March" },
    { value: "4", label: "April" },
    { value: "5", label: "May" },
    { value: "6", label: "June" },
    { value: "7", label: "July" },
    { value: "8", label: "August" },
    { value: "9", label: "September" },
    { value: "10", label: "October" },
    { value: "11", label: "November" },
    { value: "12", label: "December" },
  ];

  const fetchTransactions = async (isCalculate = false) => {
    setLoading(true);
    try {
      let url = `/${boxSlug}/calculate-leave-transactions-all`;
      const queryParams: string[] = [];
      if (selectedYear) queryParams.push(`year=${selectedYear}`);
      if (selectedMonth) queryParams.push(`month=${selectedMonth}`);
      if (queryParams.length > 0) {
        url += `?${queryParams.join("&")}`;
      }

      const res: any = await apiClient.get(url);
      if (res?.data && Array.isArray(res.data)) {
        setTransactions(res.data);
      } else if (Array.isArray(res)) {
        setTransactions(res);
      } else {
        setTransactions([]);
      }

      if (res?.summary) {
        const sumArray = Array.isArray(res.summary)
          ? res.summary
          : Object.values(res.summary);
        setSummaryData(sumArray);
      } else {
        setSummaryData([]);
      }

      if (isCalculate) {
        toast.success("Leave transactions calculated & filtered");
      }
    } catch (err: any) {
      console.error("Failed to load company leave transactions", err);
      toast.error(err.message || "Failed to load transactions");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchTransactions();
    }
  }, [boxSlug]);

  const filteredTransactions = useMemo(() => {
    return transactions.filter((t) => {
      const empName = t.employee_name || t.box_user?.user?.name || "";
      const empId = t.employee_id || t.box_user?.employee_id || "";
      const dept = t.department || t.box_user?.departmentRelation?.name || "";
      const remarks = t.transaction_remarks || t.remarks || "";
      const type = t.leave_type || t.leave_type_name || "";

      return (
        !searchTerm ||
        empName.toLowerCase().includes(searchTerm.toLowerCase()) ||
        empId.toLowerCase().includes(searchTerm.toLowerCase()) ||
        dept.toLowerCase().includes(searchTerm.toLowerCase()) ||
        remarks.toLowerCase().includes(searchTerm.toLowerCase()) ||
        type.toLowerCase().includes(searchTerm.toLowerCase())
      );
    });
  }, [transactions, searchTerm]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Leave Transactions All
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Company-wide leave credits ledger, calculate periodic credits, and audit balances.
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Button
            variant="outline"
            size="sm"
            onClick={() => fetchTransactions(false)}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>
        </div>
      </div>

      {/* Control / Calculation Toolbar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-5">
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div className="space-y-1.5">
              <label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                Select Month
              </label>
              <Select value={selectedMonth} onValueChange={(val) => setSelectedMonth(val || "")}>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="All Months" />
                </SelectTrigger>
                <SelectContent>
                  {months.map((m) => (
                    <SelectItem key={m.value} value={m.value}>
                      {m.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                Select Year
              </label>
              <Select value={selectedYear} onValueChange={(val) => setSelectedYear(val || String(currentYear))}>
                <SelectTrigger className="w-full">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {[currentYear + 1, currentYear, currentYear - 1, currentYear - 2].map(
                    (y) => (
                      <SelectItem key={y} value={String(y)}>
                        {y}
                      </SelectItem>
                    )
                  )}
                </SelectContent>
              </Select>
            </div>

            <div className="flex gap-2 col-span-1 sm:col-span-2">
              <Button
                onClick={() => fetchTransactions(true)}
                className="gap-2 flex-1 bg-primary text-primary-foreground"
              >
                <Calculator className="h-4 w-4" />
                Calculate / Filter
              </Button>
              <Button
                variant="outline"
                onClick={() => {
                  setSelectedMonth("");
                  setSelectedYear(String(currentYear));
                  fetchTransactions(false);
                }}
              >
                Reset
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Credit Summary Card (if summary data exists) */}
      {summaryData.length > 0 && (
        <Card className="border-border/60 shadow-sm overflow-hidden">
          <CardHeader className="bg-muted/30 pb-3">
            <CardTitle className="text-sm font-semibold flex items-center gap-2">
              <Coins className="h-4 w-4 text-primary" />
              Calculated Leave Credits Summary
            </CardTitle>
          </CardHeader>
          <div className="overflow-x-auto">
            <Table>
              <TableHeader className="bg-muted/20">
                <TableRow>
                  <TableHead>Employee ID</TableHead>
                  <TableHead>Employee Name</TableHead>
                  <TableHead className="text-right">Total Credits</TableHead>
                  <TableHead className="text-right">Credit Count</TableHead>
                  <TableHead>Current Balances</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {summaryData.map((item, idx) => (
                  <TableRow key={idx} className="hover:bg-muted/20">
                    <TableCell className="font-mono text-xs font-semibold">
                      {item.employee_id || item.employeeId || "-"}
                    </TableCell>
                    <TableCell className="font-medium text-sm">
                      {item.employee_name || item.name || "-"}
                    </TableCell>
                    <TableCell className="text-right font-semibold text-emerald-600">
                      +{item.total_credits ?? item.totalCredits ?? 0}
                    </TableCell>
                    <TableCell className="text-right font-mono text-xs">
                      {item.credit_count ?? item.creditCount ?? 0}
                    </TableCell>
                    <TableCell>
                      {item.current_balances ? (
                        <div className="flex flex-wrap gap-1">
                          {Object.entries(item.current_balances).map(([type, bal]) => (
                            <Badge key={type} variant="secondary" className="text-xs">
                              {type}: {String(bal)}
                            </Badge>
                          ))}
                        </div>
                      ) : (
                        <span className="text-muted-foreground text-xs">-</span>
                      )}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </Card>
      )}

      {/* Transactions Search Bar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by employee, ID, department, or remarks..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>
          <span className="text-xs text-muted-foreground">
            Total {filteredTransactions.length} transaction entries
          </span>
        </CardContent>
      </Card>

      {/* Detailed Transactions Table */}
      <Card className="border-border/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader className="bg-muted/40">
              <TableRow>
                <TableHead className="w-[80px]">Sr No.</TableHead>
                <TableHead>Employee</TableHead>
                <TableHead>Department</TableHead>
                <TableHead>Leave Type</TableHead>
                <TableHead>Txn Date</TableHead>
                <TableHead className="text-right">Credit Leave</TableHead>
                <TableHead className="text-right">Prev Balance</TableHead>
                <TableHead className="text-right">New Balance</TableHead>
                <TableHead>Remarks</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={9} className="text-center py-12 text-muted-foreground">
                    <div className="flex items-center justify-center gap-2">
                      <RefreshCw className="h-4 w-4 animate-spin" />
                      Loading company leave transactions...
                    </div>
                  </TableCell>
                </TableRow>
              ) : filteredTransactions.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={9} className="text-center py-12 text-muted-foreground">
                    No transactions found for the selected criteria.
                  </TableCell>
                </TableRow>
              ) : (
                filteredTransactions.map((item, idx) => {
                  const empName = item.employee_name || item.box_user?.user?.name || "-";
                  const empId = item.employee_id || item.box_user?.employee_id || "";
                  const dept = item.department || item.box_user?.departmentRelation?.name || "-";
                  const leaveType = item.leave_type || item.leave_type_name || "-";
                  const credit = Number(item.credit_leave || 0);

                  return (
                    <TableRow key={item.id || idx} className="hover:bg-muted/30">
                      <TableCell className="font-mono text-xs text-muted-foreground">
                        {idx + 1}
                      </TableCell>
                      <TableCell>
                        <div className="font-medium text-sm">{empName}</div>
                        {empId && (
                          <div className="text-xs text-muted-foreground font-mono">
                            {empId}
                          </div>
                        )}
                      </TableCell>
                      <TableCell className="text-sm text-muted-foreground">{dept}</TableCell>
                      <TableCell className="font-medium text-sm">{leaveType}</TableCell>
                      <TableCell className="font-mono text-xs text-muted-foreground">
                        {item.date || item.transaction_date || "-"}
                      </TableCell>
                      <TableCell className="text-right font-semibold text-emerald-600">
                        {credit > 0 ? `+${credit}` : "-"}
                      </TableCell>
                      <TableCell className="text-right font-mono text-xs text-muted-foreground">
                        {item.previous_balance ?? item.opening_balance ?? "-"}
                      </TableCell>
                      <TableCell className="text-right font-semibold text-sm">
                        {item.new_balance ?? item.balance_leave ?? "-"}
                      </TableCell>
                      <TableCell className="text-sm text-muted-foreground max-w-[200px] truncate" title={item.remarks || item.transaction_remarks}>
                        {item.remarks || item.transaction_remarks || "-"}
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
