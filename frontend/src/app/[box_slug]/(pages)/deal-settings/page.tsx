"use client";

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';
import { Plus, Pencil, Trash2, Check, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Badge } from '@/components/ui/badge';

interface SettingTabProps {
  title: string;
  endpoint: string;
  data: any[];
  onRefresh: () => void;
  routeBoxSlug: string;
}

function SettingTab({ title, endpoint, data, onRefresh, routeBoxSlug }: SettingTabProps) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [selectedItem, setSelectedItem] = useState<any>(null);
  
  const [name, setName] = useState('');
  const [status, setStatus] = useState('active'); // active or inactive
  
  const handleEdit = (item: any) => {
    setSelectedItem(item);
    setName(item.name);
    setStatus(String(item.status).toLowerCase() === 'inactive' ? 'inactive' : 'active');
    setIsModalOpen(true);
  };
  
  const handleAddNew = () => {
    setSelectedItem(null);
    setName('');
    setStatus('active');
    setIsModalOpen(true);
  };
  
  const confirmDelete = (item: any) => {
    setSelectedItem(item);
    setIsDeleteModalOpen(true);
  };
  
  const onSubmit = async () => {
    if (!name.trim()) {
      toast.error('Name is required');
      return;
    }
    
    try {
      const payload = {
        name,
        status: status
      };
      
      if (selectedItem) {
        await apiClient.put(`/${routeBoxSlug}/manage-users/deal-settings/${endpoint}/${selectedItem.id}`, payload);
        toast.success(`${title} updated successfully`);
      } else {
        await apiClient.post(`/${routeBoxSlug}/manage-users/deal-settings/${endpoint}`, payload);
        toast.success(`${title} added successfully`);
      }
      
      setIsModalOpen(false);
      onRefresh();
    } catch (error: any) {
      toast.error(error?.message || `Failed to save ${title}`);
    }
  };
  
  const handleDelete = async () => {
    if (!selectedItem) return;
    try {
      await apiClient.delete(`/${routeBoxSlug}/manage-users/deal-settings/${endpoint}/${selectedItem.id}`, {
        headers: { 'Content-Type': 'application/json' }
      });
      toast.success(`${title} deleted successfully`);
      setIsDeleteModalOpen(false);
      onRefresh();
    } catch (error: any) {
      toast.error(error?.message || `Failed to delete ${title}`);
    }
  };
  
  return (
    <div className="bg-white rounded-md border shadow-sm p-4 mt-4 min-h-[400px]">
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-medium">{title}</h3>
        <Button onClick={handleAddNew} size="sm"><Plus className="h-4 w-4 mr-2"/> Add {title}</Button>
      </div>
      
      <div className="table-responsive">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Sr. No.</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.map((item, index) => (
              <TableRow key={item.id}>
                <TableCell>{index + 1}</TableCell>
                <TableCell className="font-medium text-primary cursor-pointer hover:underline" onClick={() => handleEdit(item)}>{item.name}</TableCell>
                <TableCell>
                  {String(item.status).toLowerCase() === 'active' || String(item.status) === '1' || item.status === true || item.status === 'true' ? (
                    <Badge className="bg-green-100 text-green-700 hover:bg-green-100 border-green-200 border"><Check className="mr-1 h-3 w-3"/> Active</Badge>
                  ) : (
                    <Badge variant="secondary" className="border"><X className="mr-1 h-3 w-3"/> Inactive</Badge>
                  )}
                </TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="icon" onClick={() => handleEdit(item)}>
                      <Pencil className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon" onClick={() => confirmDelete(item)}>
                      <Trash2 className="h-4 w-4 text-destructive" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
            {data.length === 0 && (
              <TableRow>
                <TableCell colSpan={4} className="text-center py-6 text-muted-foreground">
                  No records found.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
      
      {/* Edit/Add Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{selectedItem ? `Edit ${title}` : `Add New ${title}`}</DialogTitle>
          </DialogHeader>
          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <label className="text-sm font-medium">Name <span className="text-destructive">*</span></label>
              <Input value={name} onChange={(e) => setName(e.target.value)} placeholder={`Enter ${title} Name`} />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium">Status <span className="text-destructive">*</span></label>
              <Select value={status} onValueChange={(v) => setStatus(v || 'active')}>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Select status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="active">Active</SelectItem>
                  <SelectItem value="inactive">Inactive</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsModalOpen(false)}>Cancel</Button>
            <Button onClick={onSubmit}>Save</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
      
      {/* Delete Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete {title}</DialogTitle>
          </DialogHeader>
          <div className="py-4 text-center">
            <div className="mx-auto w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
              <Trash2 className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-bold mb-2">Remove Record?</h4>
            <p className="text-muted-foreground">Are you sure you want to remove this record?</p>
          </div>
          <DialogFooter className="flex justify-center sm:justify-center">
            <Button variant="outline" onClick={() => setIsDeleteModalOpen(false)}>Cancel</Button>
            <Button variant="destructive" onClick={handleDelete}>Delete</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}

export default function DealSettingsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';
  
  const [settingsData, setSettingsData] = useState<any>({
    statuses: [],
    periods: [],
    sources: [],
    priorities: [],
    tags: []
  });
  
  const fetchSettings = async () => {
    try {
      const response = await apiClient.get(`/${routeBoxSlug}/manage-users/deal-settings/json`);
      setSettingsData({
        statuses: response?.statuses || [],
        periods: response?.periods || [],
        sources: response?.sources || [],
        priorities: response?.priorities || [],
        tags: response?.tags || [],
      });
    } catch (error) {
      console.error('Failed to load deal settings', error);
      toast.error('Failed to load settings');
    }
  };
  
  useEffect(() => {
    if (routeBoxSlug) {
      fetchSettings();
    }
  }, [routeBoxSlug]);

  return (
    <div className="p-6 bg-slate-50 min-h-screen">
      <div className="mb-6 border-b pb-4">
        <h4 className="text-xl font-bold">Deal Settings</h4>
        <p className="text-muted-foreground text-sm mt-1">Manage global settings for deals</p>
      </div>
      
      <Tabs defaultValue="statuses" className="w-full">
        <TabsList className="w-full justify-start overflow-x-auto bg-white border h-12 p-1">
          <TabsTrigger value="statuses" className="data-[state=active]:bg-primary data-[state=active]:text-white">Statuses</TabsTrigger>
          <TabsTrigger value="periods" className="data-[state=active]:bg-primary data-[state=active]:text-white">Periods</TabsTrigger>
          <TabsTrigger value="sources" className="data-[state=active]:bg-primary data-[state=active]:text-white">Sources</TabsTrigger>
          <TabsTrigger value="priorities" className="data-[state=active]:bg-primary data-[state=active]:text-white">Priorities</TabsTrigger>
          <TabsTrigger value="tags" className="data-[state=active]:bg-primary data-[state=active]:text-white">Tags</TabsTrigger>
        </TabsList>
        
        <TabsContent value="statuses">
          <SettingTab title="Status" endpoint="status" data={settingsData.statuses} onRefresh={fetchSettings} routeBoxSlug={routeBoxSlug} />
        </TabsContent>
        <TabsContent value="periods">
          <SettingTab title="Period" endpoint="period" data={settingsData.periods} onRefresh={fetchSettings} routeBoxSlug={routeBoxSlug} />
        </TabsContent>
        <TabsContent value="sources">
          <SettingTab title="Source" endpoint="source" data={settingsData.sources} onRefresh={fetchSettings} routeBoxSlug={routeBoxSlug} />
        </TabsContent>
        <TabsContent value="priorities">
          <SettingTab title="Priority" endpoint="priority" data={settingsData.priorities} onRefresh={fetchSettings} routeBoxSlug={routeBoxSlug} />
        </TabsContent>
        <TabsContent value="tags">
          <SettingTab title="Tag" endpoint="tag" data={settingsData.tags} onRefresh={fetchSettings} routeBoxSlug={routeBoxSlug} />
        </TabsContent>
      </Tabs>
    </div>
  );
}
