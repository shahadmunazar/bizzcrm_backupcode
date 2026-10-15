"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  Calendar as CalendarIcon,
  RefreshCw,
  Search,
  ArrowDownRight,
  ArrowUpRight,
  Wallet,
  Clock,
  CheckCircle2,
  Filter,
  FileText,
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
import { Card, CardContent } from "@/components/ui/card";

export default function LeaveTransactionPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const currentYear = new Date().getFullYear();
  const [selectedYear, setSelectedYear] = useState(String(currentYear));
  const [loading, setLoading] = useState(true);
  const [transactions, setTransactions] = useState<any[]>([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [typeFilter, setTypeFilter] = useState("all");

  const fetchTransactions = async () => {
    setLoading(true);
    try {
      const res: any = await apiClient.get(
        `/${boxSlug}/leaves/transactions?year=${selectedYear}`
      );
      let list: any[] = [];
      if (res?.leaveTransactions) {
        if (Array.isArray(res.leaveTransactions)) {
          list = res.leaveTransactions;
        } else if (res.leaveTransactions[selectedYear]) {
          list = res.leaveTransactions[selectedYear];
        } else {
          list = Object.values(res.leaveTransactions).flat();
        }
      } else if (res?.data && Array.isArray(res.data)) {
        list = res.data;
      } else if (Array.isArray(res)) {
        list = res;
      }
      setTransactions(list);
    } catch (err: any) {
      console.error("Failed to fetch leave transactions", err);
      toast.error(err.message || "Failed to load transactions");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchTransactions();
    }
  }, [boxSlug, selectedYear]);

  // Aggregate stats
  const totals = useMemo(() => {
    let credit = 0;
    let debit = 0;
    let latestBalance = 0;

    transactions.forEach((t) => {
      credit += Number(t.credit_leave || 0);
      debit += Number(t.debit_leave || 0);
    });

    if (transactions.length > 0) {
      // Find latest balance
      latestBalance = Number(transactions[0].balance_leave ?? (credit - debit));
    }

    return { credit, debit, balance: credit - debit };
  }, [transactions]);

  // Unique leave types from transactions
  const uniqueTypes = useMemo(() => {
    const set = new Set<string>();
    transactions.forEach((t) => {
      const typeName = t.leave_type?.leave_type || t.leave_type || "";
      if (typeName) set.add(typeName);
    });
    return Array.from(set);
  }, [transactions]);

  const filteredTransactions = useMemo(() => {
    return transactions.filter((t) => {
      const typeName = t.leave_type?.leave_type || t.leave_type || "";
      const typeMatch = typeFilter === "all" || typeName === typeFilter;
      const remarks = t.transaction_remarks || "";
      const searchMatch =
        !searchTerm ||
        remarks.toLowerCase().includes(searchTerm.toLowerCase()) ||
        typeName.toLowerCase().includes(searchTerm.toLowerCase());
      return typeMatch && searchMatch;
    });
  }, [transactions, typeFilter, searchTerm]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Page Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Leave Transactions
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            View your personal leave credits, debits, and balance ledger history.
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
              {[currentYear + 1, currentYear, currentYear - 1, currentYear - 2].map((y) => (
                <SelectItem key={y} value={String(y)}>
                  {y}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>

          <Button
            variant="outline"
            size="sm"
            onClick={fetchTransactions}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>
        </div>
      </div>

      {/* Summary KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-emerald-50/50 via-background to-background dark:from-emerald-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Total Credited
                </p>
                <h3 className="text-2xl font-bold mt-1 text-emerald-600">
                  +{totals.credit} Days
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                <ArrowUpRight className="h-5 w-5" />
              </div>
            </div>
            <p className="mt-2 text-xs text-muted-foreground">
              Credits earned or granted in {selectedYear}
            </p>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-rose-50/50 via-background to-background dark:from-rose-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Total Debited
                </p>
                <h3 className="text-2xl font-bold mt-1 text-rose-600">
                  -{totals.debit} Days
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center">
                <ArrowDownRight className="h-5 w-5" />
              </div>
            </div>
            <p className="mt-2 text-xs text-muted-foreground">
              Leaves taken or deducted in {selectedYear}
            </p>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-blue-50/50 via-background to-background dark:from-blue-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Net Balance
                </p>
                <h3 className="text-2xl font-bold mt-1 text-blue-600">
                  {totals.balance} Days
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center">
                <Wallet className="h-5 w-5" />
              </div>
            </div>
            <p className="mt-2 text-xs text-muted-foreground">
              Remaining available leave balance
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Filter Bar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex flex-col sm:flex-row items-center gap-3">
          <div className="relative flex-1 w-full">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by remarks or leave type..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>

          <div className="w-full sm:w-auto">
            <Select value={typeFilter} onValueChange={(val) => setTypeFilter(val || "all")}>
              <SelectTrigger className="w-full sm:w-[180px] h-9">
                <SelectValue placeholder="All Leave Types" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Leave Types</SelectItem>
                {uniqueTypes.map((t) => (
                  <SelectItem key={t} value={t}>
                    {t}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Ledger Table */}
      <Card className="border-border/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader className="bg-muted/40">
              <TableRow>
                <TableHead className="w-[120px]">Date</TableHead>
                <TableHead>Leave Type</TableHead>
                <TableHead className="min-w-[220px]">Transaction Remarks</TableHead>
                <TableHead className="text-right">Credit</TableHead>
                <TableHead className="text-right">Debit</TableHead>
                <TableHead className="text-right">Prev Balance</TableHead>
                <TableHead className="text-right">Balance</TableHead>
                <TableHead>Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-12 text-muted-foreground">
                    <div className="flex items-center justify-center gap-2">
                      <RefreshCw className="h-4 w-4 animate-spin" />
                      Loading leave ledger...
                    </div>
                  </TableCell>
                </TableRow>
              ) : filteredTransactions.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-12 text-muted-foreground">
                    No transactions recorded for {selectedYear}.
                  </TableCell>
                </TableRow>
              ) : (
                filteredTransactions.map((t, idx) => {
                  const credit = Number(t.credit_leave || 0);
                  const debit = Number(t.debit_leave || 0);
                  const typeName = t.leave_type?.leave_type || t.leave_type || "-";
                  const statusStr = (t.status || "Approved").toLowerCase();

                  return (
                    <TableRow key={t.id || idx} className="hover:bg-muted/30">
                      <TableCell className="font-mono text-xs">
                        {t.date
                          ? new Date(t.date).toLocaleDateString("en-US", {
                              day: "2-digit",
                              month: "short",
                              year: "numeric",
                            })
                          : "-"}
                      </TableCell>
                      <TableCell className="font-medium text-sm">
                        {typeName}
                      </TableCell>
                      <TableCell className="text-sm text-muted-foreground">
                        {t.transaction_remarks || "-"}
                      </TableCell>
                      <TableCell className="text-right font-medium">
                        {credit > 0 ? (
                          <span className="text-emerald-600 font-semibold">
                            +{credit}
                          </span>
                        ) : (
                          <span className="text-muted-foreground/50">-</span>
                        )}
                      </TableCell>
                      <TableCell className="text-right font-medium">
                        {debit > 0 ? (
                          <span className="text-rose-600 font-semibold">
                            -{debit}
                          </span>
                        ) : (
                          <span className="text-muted-foreground/50">-</span>
                        )}
                      </TableCell>
                      <TableCell className="text-right font-mono text-xs text-muted-foreground">
                        {t.opening_balance ?? "-"}
                      </TableCell>
                      <TableCell className="text-right font-semibold text-sm">
                        {t.balance_leave ?? "-"}
                      </TableCell>
                      <TableCell>
                        {statusStr === "approved" ? (
                          <Badge className="bg-emerald-500/10 text-emerald-600 border-emerald-200">
                            Approved
                          </Badge>
                        ) : statusStr === "pending" ? (
                          <Badge className="bg-amber-500/10 text-amber-600 border-amber-200">
                            Pending
                          </Badge>
                        ) : (
                          <Badge variant="secondary">
                            {t.status || "Recorded"}
                          </Badge>
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
