"use client";

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';
import { Search, Plus, Pencil, Trash2, FileType, FileText, GripVertical } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";

interface FormValues {
  id?: number;
  name: string;
  pipeline_id: string;
  status: string; // Maps to status_id in backend
  deal_value: string;
  currency: string;
  period: string; // Maps to period_id
  period_value: string;
  lead_id: string;
  contact_id: string;
  project_id: string;
  due_date: string;
  expected_close_date: string;
  follow_up_date: string;
  assignees: number[];
  source: string; // Maps to source_id
  tags: number[];
  priority: string; // Maps to priority_id
  description: string;
}

export default function DealsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [deals, setDeals] = useState<any[]>([]);
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 20;

  const [options, setOptions] = useState<any>({
    pipelines: [],
    projects: [],
    contacts: [],
    users: [],
    statuses: [],
    periods: [],
    sources: [],
    priorities: [],
    tags: []
  });

  const [isLoading, setIsLoading] = useState(true);
  const [filterTitle, setFilterTitle] = useState('');
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const [isPipelineModalOpen, setIsPipelineModalOpen] = useState(false);
  const [newPipelineName, setNewPipelineName] = useState('');
  const [pipelineStages, setPipelineStages] = useState<string[]>(['Inpipeline', 'Follow Up', 'Schedule Service']);
  const [pipelineAccess, setPipelineAccess] = useState<'all' | 'specific'>('all');
  const [pipelineAccessUsers, setPipelineAccessUsers] = useState<string[]>([]);
  
  const [isStageModalOpen, setIsStageModalOpen] = useState(false);
  const [newStageName, setNewStageName] = useState('');

  const [leadSearchQuery, setLeadSearchQuery] = useState('');
  const [leadSearchResults, setLeadSearchResults] = useState<any[]>([]);
  const [isSearchingLeads, setIsSearchingLeads] = useState(false);
  const [selectedLeadName, setSelectedLeadName] = useState('');
  const [leadSearchPage, setLeadSearchPage] = useState(1);
  const [hasMoreLeads, setHasMoreLeads] = useState(true);
  const [isLeadDropdownOpen, setIsLeadDropdownOpen] = useState(false);

  const emptyForm: FormValues = {
    name: '',
    pipeline_id: '',
    status: '',
    deal_value: '',
    currency: '',
    period: '',
    period_value: '',
    lead_id: '',
    contact_id: '',
    project_id: '',
    due_date: '',
    expected_close_date: '',
    follow_up_date: '',
    assignees: [],
    source: '',
    tags: [],
    priority: '',
    description: ''
  };

  const [form, setForm] = useState<FormValues>(emptyForm);

  const fetchOptions = async () => {
    try {
      const res = await apiClient.get(`/${routeBoxSlug}/manage-users/deals-form-options`);
      if (res && res.success) {
        setOptions({
          pipelines: res.pipelines || [],
          projects: res.projects || [],
          contacts: res.contacts || [],
          users: res.users || [],
          statuses: res.statuses || [],
          periods: res.periods || [],
          sources: res.sources || [],
          priorities: res.priorities || [],
          tags: res.tags || []
        });
      }
    } catch (error) {
      console.error('Failed to fetch options:', error);
    }
  };

  const fetchDeals = async () => {
    try {
      setIsLoading(true);
      const response = await apiClient.get(`/${routeBoxSlug}/manage-users/deals-json`);
      setDeals(Array.isArray(response) ? response : (response?.data?.data || response?.data || []));
    } catch (error) {
      console.error('Failed to fetch deals:', error);
      toast.error('Failed to load deals');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchOptions();
      fetchDeals();
    }
  }, [routeBoxSlug]);

  const handleEdit = (item: any) => {
    let tgs: number[] = [];
    let asg: number[] = [];
    try {
      const pTgs = typeof item.tags === 'string' ? JSON.parse(item.tags) : (item.tags || []);
      const pAsg = typeof item.assignees === 'string' ? JSON.parse(item.assignees) : (item.assignees || []);
      tgs = Array.isArray(pTgs) ? pTgs : [];
      asg = Array.isArray(pAsg) ? pAsg : [];
    } catch(e) {}

    setForm({
      id: item.id,
      name: item.name || '',
      pipeline_id: String(item.pipeline_id || ''),
      status: String(item.status_id || ''),
      deal_value: item.deal_value || '',
      currency: item.currency || '',
      period: String(item.period_id || ''),
      period_value: item.period_value || '',
      lead_id: String(item.lead_id || ''),
      contact_id: String(item.contact_id || ''),
      project_id: String(item.project_id || ''),
      due_date: item.due_date ? item.due_date.split(' ')[0] : '',
      expected_close_date: item.expected_close_date ? item.expected_close_date.split(' ')[0] : '',
      follow_up_date: item.follow_up_date ? item.follow_up_date.split(' ')[0] : '',
      assignees: asg,
      source: String(item.source_id || ''),
      tags: tgs,
      priority: String(item.priority_id || ''),
      description: item.description || ''
    });
    setLeadSearchQuery('');
    setSelectedLeadName(item.lead_name || item.lead?.name || '');
    setIsModalOpen(true);
  };

  const confirmDelete = (id: number) => {
    setDeleteId(id);
    setIsDeleteModalOpen(true);
  };

  const handleDelete = async () => {
    if (!deleteId) return;
    try {
      await apiClient.delete(`/${routeBoxSlug}/manage-users/deals/${deleteId}`, {
        headers: { 'Content-Type': 'application/json' }
      });
      toast.success('Deal deleted successfully');
      setIsDeleteModalOpen(false);
      fetchDeals();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete deal');
    }
  };

  const onSubmit = async () => {
    try {
      if (!form.name.trim()) {
        toast.error('Deal Name is required');
        return;
      }
      
      const payload = { ...form };
      
      if (form.id) {
        await apiClient.put(`/${routeBoxSlug}/manage-users/deals/${form.id}`, payload);
        toast.success('Deal updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/manage-users/deals`, payload);
        toast.success('Deal created successfully');
      }
      
      setIsModalOpen(false);
      fetchDeals();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save deal');
    }
  };

  const onAddPipeline = async () => {
    if (!newPipelineName.trim()) {
      toast.error('Pipeline Name is required');
      return;
    }
    try {
      const res = await apiClient.post(`/${routeBoxSlug}/manage-users/pipelines`, {
        name: newPipelineName,
        stages: pipelineStages,
        access: pipelineAccess,
        access_users: pipelineAccessUsers
      });
      toast.success('Pipeline created successfully');
      setIsPipelineModalOpen(false);
      setNewPipelineName('');
      setPipelineStages(['Inpipeline', 'Follow Up', 'Schedule Service']);
      setPipelineAccess('all');
      setPipelineAccessUsers([]);
      await fetchOptions(); // refresh the dropdowns
      if (res && res.data && res.data.id) {
        setForm({ ...form, pipeline_id: String(res.data.id) });
      }
    } catch (error: any) {
      toast.error(error?.message || 'Failed to create pipeline');
    }
  };

  const fetchLeads = async (query: string, page: number, append: boolean = false) => {
    try {
      setIsSearchingLeads(true);
      const res = await apiClient.get(`/${routeBoxSlug}/manage-users/deals-leads-search?q=${encodeURIComponent(query)}&page=${page}`);
      
      const newLeads = res?.results || [];
      const hasMore = res?.pagination?.more || false;
      
      if (append) {
        setLeadSearchResults(prev => [...prev, ...newLeads]);
      } else {
        setLeadSearchResults(newLeads);
      }
      
      setHasMoreLeads(hasMore);
      setLeadSearchPage(page);
    } catch (error) {
      console.error('Lead search failed', error);
    } finally {
      setIsSearchingLeads(false);
    }
  };

  const handleLeadSearch = (query: string) => {
    setLeadSearchQuery(query);
    fetchLeads(query, 1, false);
  };

  const handleLeadInputFocus = () => {
    setIsLeadDropdownOpen(true);
    if (leadSearchResults.length === 0) {
      fetchLeads(leadSearchQuery, 1, false);
    }
  };

  const handleLeadScroll = (e: React.UIEvent<HTMLDivElement>) => {
    const { scrollTop, scrollHeight, clientHeight } = e.currentTarget;
    if (scrollHeight - scrollTop <= clientHeight + 20 && !isSearchingLeads && hasMoreLeads) {
      fetchLeads(leadSearchQuery, leadSearchPage + 1, true);
    }
  };

  const filteredDeals = deals.filter(d => d.name?.toLowerCase().includes(filterTitle.toLowerCase()));
  const totalPages = Math.ceil(filteredDeals.length / itemsPerPage);
  const paginatedDeals = filteredDeals.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  const renderPaginationItems = () => {
    if (totalPages <= 1) return null;
    const pages = [];
    pages.push(1);
    if (currentPage > 3) pages.push('ellipsis-start');
    for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
      pages.push(i);
    }
    if (currentPage < totalPages - 2) pages.push('ellipsis-end');
    if (totalPages > 1) pages.push(totalPages);
    
    return pages.map((page, idx) => {
      if (typeof page === 'string') {
        return (
          <PaginationItem key={`${page}-${idx}`}>
            <PaginationEllipsis />
          </PaginationItem>
        );
      }
      return (
        <PaginationItem key={page}>
          <PaginationLink 
            isActive={page === currentPage} 
            onClick={() => setCurrentPage(page)}
            className="cursor-pointer"
          >
            {page}
          </PaginationLink>
        </PaginationItem>
      );
    });
  };


  const getOptionName = (list: any[], id: any) => {
    if (!id) return '-';
    const found = list.find((item: any) => String(item.id) === String(id));
    return found ? found.name : id;
  };

  return (
    <div className="p-6 bg-slate-50 min-h-screen">
      {/* Top Header */}
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
        <div className="flex items-center">
          <h4 className="text-xl font-bold flex items-center gap-2">
            Deals
            <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{deals.length}</Badge>
          </h4>
        </div>

        <div className="flex items-center gap-3 flex-wrap">
          <div className="relative">
            <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
            <Input 
              className="pl-9 bg-white" 
              placeholder="Search Deals" 
              value={filterTitle}
              onChange={(e) => {
                setFilterTitle(e.target.value);
                setCurrentPage(1);
              }}
            />
          </div>

          <DropdownMenu>
            <DropdownMenuTrigger className="inline-flex items-center justify-center whitespace-nowrap rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm bg-white">
              <FileType className="mr-2 h-4 w-4" /> Export
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuItem onClick={() => window.open(`/${routeBoxSlug}/manage-users/deals-export?type=pdf`, '_blank')}>
                <FileText className="mr-2 h-4 w-4 text-red-500" /> Export as PDF
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => window.open(`/${routeBoxSlug}/manage-users/deals-export?type=excel`, '_blank')}>
                <FileType className="mr-2 h-4 w-4 text-green-500" /> Export as Excel
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <Button onClick={() => {
            setForm(emptyForm);
            setIsModalOpen(true);
          }} className="bg-primary hover:bg-primary/90">
            <Plus className="mr-2 h-4 w-4" /> Add New Deal
          </Button>
        </div>
      </div>

      {/* Deals List */}
      <div className="bg-white rounded-md border p-4 shadow-sm min-h-[500px]">
        {isLoading ? (
          <div className="text-center py-12 text-muted-foreground">Loading deals...</div>
        ) : (
          <div className="table-responsive">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Sr. No.</TableHead>
                  <TableHead>Deal Name</TableHead>
                  <TableHead>Pipeline</TableHead>
                  <TableHead>Deal Value</TableHead>
                  <TableHead>Contact</TableHead>
                  <TableHead>Project</TableHead>
                  <TableHead>Period</TableHead>
                  <TableHead>Source</TableHead>
                  <TableHead>Priority</TableHead>
                  <TableHead>Tags</TableHead>
                  <TableHead>Due Date</TableHead>
                  <TableHead>Close Date</TableHead>
                  <TableHead>Follow Up</TableHead>
                  <TableHead>Owner</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {paginatedDeals.map((item, index) => {
                  return (
                    <TableRow key={item.id}>
                      <TableCell>{index + 1}</TableCell>
                      <TableCell className="font-medium text-primary cursor-pointer hover:underline" onClick={() => handleEdit(item)}>{item.name}</TableCell>
                      <TableCell>{getOptionName(options.pipelines, item.pipeline_id)}</TableCell>
                      <TableCell>{item.deal_value ? `${item.currency || ''} ${item.deal_value}` : '-'}</TableCell>
                      <TableCell>{getOptionName(options.contacts, item.contact_id)}</TableCell>
                      <TableCell>{getOptionName(options.projects, item.project_id)}</TableCell>
                      <TableCell>{getOptionName(options.periods, item.period_id)}</TableCell>
                      <TableCell>{getOptionName(options.sources, item.source_id)}</TableCell>
                      <TableCell>{getOptionName(options.priorities, item.priority_id)}</TableCell>
                      <TableCell>
                        {item.tags ? (
                          (() => {
                            try {
                              const parsed = typeof item.tags === 'string' ? JSON.parse(item.tags) : item.tags;
                              const ids = Array.isArray(parsed) ? parsed : [];
                              const names = ids.map((id: number) => getOptionName(options.tags, id)).filter((n: string) => n !== '-');
                              return names.length > 0 ? names.join(', ') : '-';
                            } catch(e) {
                              return '-';
                            }
                          })()
                        ) : '-'}
                      </TableCell>
                      <TableCell>{item.due_date ? new Date(item.due_date).toLocaleDateString() : '-'}</TableCell>
                      <TableCell>{item.expected_close_date ? new Date(item.expected_close_date).toLocaleDateString() : '-'}</TableCell>
                      <TableCell>{item.follow_up_date ? new Date(item.follow_up_date).toLocaleDateString() : '-'}</TableCell>
                      <TableCell>
                        {item.assignees ? (
                          (() => {
                            try {
                              const parsed = typeof item.assignees === 'string' ? JSON.parse(item.assignees) : item.assignees;
                              const ids = Array.isArray(parsed) ? parsed : [];
                              const names = ids.map((id: number) => getOptionName(options.users, id)).filter((n: string) => n !== '-');
                              return names.length > 0 ? names.join(', ') : '-';
                            } catch(e) {
                              return '-';
                            }
                          })()
                        ) : '-'}
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline" className="bg-slate-50">{getOptionName(options.statuses, item.status_id)}</Badge>
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          <Button variant="ghost" size="icon" onClick={() => handleEdit(item)}>
                            <Pencil className="h-4 w-4" />
                          </Button>
                          <Button variant="ghost" size="icon" onClick={() => confirmDelete(item.id)}>
                            <Trash2 className="h-4 w-4 text-destructive" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  );
                })}
                {paginatedDeals.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={15} className="text-center py-6 text-muted-foreground">
                      No deals found.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
            
            {totalPages > 0 && (
              <div className="p-4 border-t flex flex-col md:flex-row items-center justify-between gap-4 mt-4">
                <div className="text-sm text-muted-foreground whitespace-nowrap">
                  Showing {(currentPage - 1) * itemsPerPage + 1} to {Math.min(currentPage * itemsPerPage, filteredDeals.length)} of {filteredDeals.length} records
                </div>
                <div className="flex-1 w-full">
                  <Pagination className="justify-end">
                    <PaginationContent>
                      <PaginationItem>
                        <PaginationPrevious 
                          onClick={() => currentPage > 1 && setCurrentPage(currentPage - 1)}
                          className={currentPage <= 1 ? "pointer-events-none opacity-50" : "cursor-pointer"} 
                        />
                      </PaginationItem>
                      {renderPaginationItems()}
                      <PaginationItem>
                        <PaginationNext 
                          onClick={() => currentPage < totalPages && setCurrentPage(currentPage + 1)}
                          className={currentPage >= totalPages ? "pointer-events-none opacity-50" : "cursor-pointer"}
                        />
                      </PaginationItem>
                    </PaginationContent>
                  </Pagination>
                </div>
              </div>
            )}
          </div>
        )}
      </div>

      {/* Add/Edit Deal Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>{form.id ? 'Edit Deal' : 'Add New Deal'}</DialogTitle>
          </DialogHeader>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 py-4">
            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Deal Name <span className="text-destructive">*</span></label>
              <Input value={form.name} onChange={(e) => setForm({...form, name: e.target.value})} placeholder="Deal Name" />
            </div>

            <div className="space-y-2 lg:col-span-2">
              <div className="flex items-center justify-between">
                <label className="text-sm font-medium">Pipeline <span className="text-destructive">*</span></label>
                <button type="button" onClick={() => setIsPipelineModalOpen(true)} className="text-xs text-primary font-medium hover:underline flex items-center">
                  <Plus className="h-3 w-3 mr-1"/> Add New
                </button>
              </div>
              <Select value={form.pipeline_id} onValueChange={(v) => setForm({...form, pipeline_id: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose Pipeline">{form.pipeline_id ? getOptionName(options.pipelines, form.pipeline_id) : null}</SelectValue></SelectTrigger>
                <SelectContent>
                  {options.pipelines.map((p: any) => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 lg:col-span-2">
              <label className="text-sm font-medium">Status <span className="text-destructive">*</span></label>
              <Select value={form.status} onValueChange={(v) => setForm({...form, status: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose Status">{form.status ? getOptionName(options.statuses, form.status) : null}</SelectValue></SelectTrigger>
                <SelectContent>
                  {options.statuses.map((p: any) => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium">Deal Value <span className="text-destructive">*</span></label>
              <Input type="number" value={form.deal_value} onChange={(e) => setForm({...form, deal_value: e.target.value})} placeholder="Value" />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium">Currency <span className="text-destructive">*</span></label>
              <Select value={form.currency} onValueChange={(v) => setForm({...form, currency: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose" /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="Dollar">Dollar</SelectItem>
                  <SelectItem value="Euro">Euro</SelectItem>
                  <SelectItem value="Pound">Pound</SelectItem>
                  <SelectItem value="Rupee">Rupee</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium">Period <span className="text-destructive">*</span></label>
              <Select value={form.period} onValueChange={(v) => setForm({...form, period: v || ''})}>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Choose">
                    {form.period ? getOptionName(options.periods, form.period) : null}
                  </SelectValue>
                </SelectTrigger>
                <SelectContent>
                  {options.periods.map((p: any) => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium">Period Value <span className="text-destructive">*</span></label>
              <Input value={form.period_value} onChange={(e) => setForm({...form, period_value: e.target.value})} placeholder="Value" />
            </div>

            <div className="space-y-2 lg:col-span-4 relative">
              <label className="text-sm font-medium">Lead</label>
              {selectedLeadName ? (
                <div className="flex items-center justify-between border rounded-md px-3 py-2 bg-slate-50">
                  <span className="text-sm font-medium">{selectedLeadName}</span>
                  <button 
                    type="button" 
                    onClick={() => {
                      setSelectedLeadName('');
                      setForm({...form, lead_id: ''});
                      setLeadSearchQuery('');
                      setLeadSearchResults([]);
                      setIsLeadDropdownOpen(false);
                    }}
                    className="text-muted-foreground hover:text-destructive"
                  >
                    <Trash2 className="h-4 w-4" />
                  </button>
                </div>
              ) : (
                <div className="relative">
                  <Input 
                    value={leadSearchQuery} 
                    onChange={(e) => handleLeadSearch(e.target.value)} 
                    onFocus={handleLeadInputFocus}
                    onBlur={() => setTimeout(() => setIsLeadDropdownOpen(false), 200)}
                    placeholder="Search or select a lead..." 
                  />
                  {isLeadDropdownOpen && (
                    <div 
                      className="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-y-auto"
                      onScroll={handleLeadScroll}
                    >
                      {leadSearchResults.length > 0 ? (
                        <>
                          {leadSearchResults.map((lead: any) => (
                            <div 
                              key={lead.id} 
                              className="p-2 hover:bg-slate-100 cursor-pointer text-sm border-b last:border-b-0"
                              onMouseDown={() => { // use onMouseDown instead of onClick to fire before onBlur
                                setForm({...form, lead_id: String(lead.id)});
                                setSelectedLeadName(lead.text || lead.name || '');
                                setLeadSearchQuery('');
                                setIsLeadDropdownOpen(false);
                              }}
                            >
                              <div className="font-medium">{lead.text || lead.name}</div>
                            </div>
                          ))}
                          {isSearchingLeads && (
                            <div className="p-2 text-xs text-center text-muted-foreground bg-slate-50">Loading more...</div>
                          )}
                        </>
                      ) : (
                        <div className="p-3 text-sm text-center text-muted-foreground">
                          {isSearchingLeads ? 'Searching...' : 'No leads found.'}
                        </div>
                      )}
                    </div>
                  )}
                </div>
              )}
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Contact <span className="text-destructive">*</span></label>
              <Select value={form.contact_id} onValueChange={(v) => setForm({...form, contact_id: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose Contact">{form.contact_id ? getOptionName(options.contacts, form.contact_id) : null}</SelectValue></SelectTrigger>
                <SelectContent>
                  {options.contacts.map((c: any) => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Project <span className="text-destructive">*</span></label>
              <Select value={form.project_id} onValueChange={(v) => setForm({...form, project_id: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose Project">{form.project_id ? getOptionName(options.projects, form.project_id) : null}</SelectValue></SelectTrigger>
                <SelectContent>
                  {options.projects.map((p: any) => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Assignees (Owner) <span className="text-destructive">*</span></label>
              <div className="flex gap-2 mb-2 flex-wrap">
                {form.assignees.map((uid: number) => {
                  const user = options.users.find((u: any) => String(u.id) === String(uid));
                  return (
                    <Badge key={uid} variant="secondary" className="px-2 py-1 flex items-center gap-1">
                      {user?.name || uid}
                      <Trash2 
                        className="h-3 w-3 cursor-pointer text-muted-foreground hover:text-destructive" 
                        onClick={() => setForm({...form, assignees: form.assignees.filter(id => id !== uid)})}
                      />
                    </Badge>
                  );
                })}
              </div>
              <Select onValueChange={(v: string) => {
                if (!v) return;
                const val = parseInt(v);
                if (val && !form.assignees.includes(val)) {
                  setForm({...form, assignees: [...form.assignees, val]});
                }
              }}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Add Assignee..." /></SelectTrigger>
                <SelectContent>
                  {options.users.filter((u: any) => !form.assignees.includes(u.id)).map((u: any) => (
                    <SelectItem key={u.id} value={String(u.id)}>{u.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Tags <span className="text-destructive">*</span></label>
              <div className="flex gap-2 mb-2 flex-wrap">
                {form.tags.map((tid: number) => {
                  const tag = options.tags.find((t: any) => String(t.id) === String(tid));
                  return (
                    <Badge key={tid} variant="outline" className="px-2 py-1 flex items-center gap-1 bg-slate-50">
                      {tag?.name || tid}
                      <Trash2 
                        className="h-3 w-3 cursor-pointer text-muted-foreground hover:text-destructive" 
                        onClick={() => setForm({...form, tags: form.tags.filter(id => id !== tid)})}
                      />
                    </Badge>
                  );
                })}
              </div>
              <Select onValueChange={(v: string) => {
                if (!v) return;
                const val = parseInt(v);
                if (val && !form.tags.includes(val)) {
                  setForm({...form, tags: [...form.tags, val]});
                }
              }}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Add Tag..." /></SelectTrigger>
                <SelectContent>
                  {options.tags.filter((t: any) => !form.tags.includes(t.id)).map((t: any) => (
                    <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Follow Up Date</label>
              <Input type="date" value={form.follow_up_date} onChange={(e) => setForm({...form, follow_up_date: e.target.value})} className="w-full" />
            </div>

            <div className="space-y-2 lg:col-span-2">
              <label className="text-sm font-medium">Due Date <span className="text-destructive">*</span></label>
              <Input type="date" value={form.due_date} onChange={(e) => setForm({...form, due_date: e.target.value})} className="w-full" />
            </div>

            <div className="space-y-2 lg:col-span-2">
              <label className="text-sm font-medium">Expected Closing Date <span className="text-destructive">*</span></label>
              <Input type="date" value={form.expected_close_date} onChange={(e) => setForm({...form, expected_close_date: e.target.value})} />
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Source <span className="text-destructive">*</span></label>
              <Select value={form.source} onValueChange={(v) => setForm({...form, source: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose Source">{form.source ? getOptionName(options.sources, form.source) : null}</SelectValue></SelectTrigger>
                <SelectContent>
                  {options.sources.map((s: any) => <SelectItem key={s.id} value={String(s.id)}>{s.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            
            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Priority <span className="text-destructive">*</span></label>
              <Select value={form.priority} onValueChange={(v) => setForm({...form, priority: v || ''})}>
                <SelectTrigger className="w-full"><SelectValue placeholder="Choose Priority">{form.priority ? getOptionName(options.priorities, form.priority) : null}</SelectValue></SelectTrigger>
                <SelectContent>
                  {options.priorities.map((p: any) => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2 lg:col-span-4">
              <label className="text-sm font-medium">Description <span className="text-destructive">*</span></label>
              <textarea 
                className="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                rows={4}
                value={form.description}
                onChange={(e) => setForm({...form, description: e.target.value})}
                placeholder="Description"
              />
            </div>
          </div>
          
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsModalOpen(false)}>Cancel</Button>
            <Button onClick={onSubmit}>Create</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Deal</DialogTitle>
          </DialogHeader>
          <div className="py-4 text-center">
            <div className="mx-auto w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
              <Trash2 className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-bold mb-2">Remove Deal?</h4>
            <p className="text-muted-foreground">Are you sure you want to remove the deal you selected?</p>
          </div>
          <DialogFooter className="flex justify-center sm:justify-center">
            <Button variant="outline" onClick={() => setIsDeleteModalOpen(false)}>Cancel</Button>
            <Button variant="destructive" onClick={handleDelete}>Delete</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Add Pipeline Modal */}
      <Dialog open={isPipelineModalOpen} onOpenChange={setIsPipelineModalOpen}>
        <DialogContent className="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Add New Pipeline</DialogTitle>
          </DialogHeader>
          <div className="py-4 space-y-6">
            <div className="space-y-2">
              <label className="text-sm font-medium">Pipeline Name <span className="text-destructive">*</span></label>
              <Input 
                value={newPipelineName} 
                onChange={(e) => setNewPipelineName(e.target.value)} 
              />
            </div>
            
            <div className="space-y-4">
              <div className="flex items-center justify-between border-b pb-2">
                <h4 className="font-bold text-base">Pipeline Stages</h4>
                <button 
                  type="button" 
                  onClick={() => { setNewStageName(''); setIsStageModalOpen(true); }}
                  className="text-sm text-destructive hover:text-destructive/90 font-medium flex items-center"
                >
                  <Plus className="h-4 w-4 mr-1"/> Add New
                </button>
              </div>
              <div className="space-y-2">
                {pipelineStages.map((stage, i) => (
                  <div key={i} className="flex items-center justify-between border rounded-md p-3 bg-white shadow-sm border-l-4 border-l-orange-400">
                    <div className="flex items-center gap-2">
                      <GripVertical className="h-4 w-4 text-muted-foreground cursor-grab" />
                      <span className="text-sm font-medium">{stage}</span>
                    </div>
                    <button 
                      type="button" 
                      onClick={() => setPipelineStages(pipelineStages.filter((_, index) => index !== i))}
                      className="text-sm text-destructive hover:bg-destructive/10 p-1 px-2 rounded flex items-center"
                    >
                      <Trash2 className="h-3 w-3 mr-1" /> Delete
                    </button>
                  </div>
                ))}
              </div>
            </div>

            <div className="space-y-4">
              <h4 className="font-bold text-base border-b pb-2">Access</h4>
              <RadioGroup value={pipelineAccess} onValueChange={(v: 'all' | 'specific') => setPipelineAccess(v)} className="flex items-center gap-4">
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="all" id="r-all" />
                  <Label htmlFor="r-all">All</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="specific" id="r-specific" />
                  <Label htmlFor="r-specific">Select Person</Label>
                </div>
              </RadioGroup>
              
              {pipelineAccess === 'specific' && (
                <div className="mt-4">
                  <label className="text-sm font-medium mb-2 block">Assign Users</label>
                  <div className="flex gap-2 mb-2 flex-wrap">
                    {pipelineAccessUsers.map(uid => {
                      const user = options.users.find((u: any) => String(u.id) === String(uid));
                      return (
                        <Badge key={uid} variant="secondary" className="px-2 py-1 flex items-center gap-1">
                          {user?.name || uid}
                          <Trash2 
                            className="h-3 w-3 cursor-pointer text-muted-foreground hover:text-destructive" 
                            onClick={() => setPipelineAccessUsers(pipelineAccessUsers.filter(id => id !== uid))}
                          />
                        </Badge>
                      );
                    })}
                  </div>
                  <Select onValueChange={(v: string) => {
                    if (!v) return;
                    const val = v;
                    if (val && !pipelineAccessUsers.includes(val)) {
                      setPipelineAccessUsers([...pipelineAccessUsers, val]);
                    }
                  }}>
                    <SelectTrigger className="w-full"><SelectValue placeholder="Add User..." /></SelectTrigger>
                    <SelectContent>
                      {options.users.filter((u: any) => !pipelineAccessUsers.includes(String(u.id))).map((u: any) => (
                        <SelectItem key={u.id} value={String(u.id)}>{u.name}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              )}
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsPipelineModalOpen(false)}>Cancel</Button>
            <Button onClick={onAddPipeline} className="bg-red-600 hover:bg-red-700 text-white">Create</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
      
      {/* Add Stage Modal */}
      <Dialog open={isStageModalOpen} onOpenChange={setIsStageModalOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Add New Stage</DialogTitle>
          </DialogHeader>
          <div className="py-4 space-y-4">
            <div className="space-y-2">
              <label className="text-sm font-medium">Stage Name <span className="text-destructive">*</span></label>
              <Input 
                value={newStageName} 
                onChange={(e) => setNewStageName(e.target.value)} 
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsStageModalOpen(false)}>Cancel</Button>
            <Button onClick={() => {
              if (newStageName.trim()) {
                setPipelineStages([...pipelineStages, newStageName.trim()]);
                setIsStageModalOpen(false);
              }
            }} className="bg-red-600 hover:bg-red-700 text-white">Save Changes</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
