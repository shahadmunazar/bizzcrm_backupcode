'use client';

import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { toast } from 'sonner';
import { apiClient } from '@/lib/api-client';
import { useParams, useRouter } from 'next/navigation';
import { Plus, Shield, ShieldCheck, MoreVertical, Edit, Trash2 } from 'lucide-react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export default function RolesPermissionsDashboard() {
  const params = useParams();
  const router = useRouter();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [isLoading, setIsLoading] = useState(true);
  const [roles, setRoles] = useState<any[]>([]);
  const [departments, setDepartments] = useState<any[]>([]);
  const [assignedCounts, setAssignedCounts] = useState<Record<number, number>>({});

  // Modals state
  const [isRoleModalOpen, setIsRoleModalOpen] = useState(false);
  const [newRoleName, setNewRoleName] = useState('');
  
  const [isDeptModalOpen, setIsDeptModalOpen] = useState(false);
  const [newDeptName, setNewDeptName] = useState('');
  
  const [isSaving, setIsSaving] = useState(false);

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    fetchData();
  }, [routeBoxSlug]);

  const fetchData = async () => {
    try {
      const res = await apiClient.get(`/${routeBoxSlug}/settings/roles-permissions/entities`);
      if (res?.success) {
        setRoles(res.roles || []);
        setDepartments(res.departments || []);
      } else {
        toast.error(res?.message || 'API returned success=false');
      }
    } catch (err: any) {
      console.error(err);
      toast.error(err?.data?.message || err?.message || 'Failed to load roles and departments');
    } finally {
      setIsLoading(false);
    }
  };

  const handleCreateRole = async () => {
    if (!newRoleName.trim()) {
      toast.error('Role name is required');
      return;
    }
    setIsSaving(true);
    try {
      const res = await apiClient.post(`/${routeBoxSlug}/settings/roles`, {
        name: newRoleName
      });
      if (res?.success) {
        toast.success(res.message || 'Role created successfully');
        setIsRoleModalOpen(false);
        setNewRoleName('');
        fetchData();
      } else {
        toast.error(res?.message || 'Failed to create role');
      }
    } catch (err: any) {
      toast.error(err?.data?.message || err?.message || 'Failed to create role');
    } finally {
      setIsSaving(false);
    }
  };

  const handleCreateDepartment = async () => {
    if (!newDeptName.trim()) {
      toast.error('Department name is required');
      return;
    }
    setIsSaving(true);
    try {
      const res = await apiClient.post(`/${routeBoxSlug}/settings/departments`, {
        name: newDeptName,
        status: '1' // Default active
      });
      if (res?.success) {
        toast.success(res.message || 'Department created successfully');
        setIsDeptModalOpen(false);
        setNewDeptName('');
        fetchData();
      } else {
        toast.error(res?.message || 'Failed to create department');
      }
    } catch (err: any) {
      toast.error(err?.data?.message || err?.message || 'Failed to create department');
    } finally {
      setIsSaving(false);
    }
  };

  const handleDeleteDepartment = async (id: number) => {
    if (!window.confirm('Are you sure you want to delete this department?')) return;
    try {
      const res = await apiClient.delete(`/${routeBoxSlug}/settings/departments/${id}`);
      if (res?.success) {
        toast.success('Department deleted');
        fetchData();
      } else {
        toast.error(res?.message || 'Failed to delete');
      }
    } catch (err: any) {
      toast.error(err?.data?.message || err?.message || 'Failed to delete department');
    }
  };

  if (isLoading) return <div className="p-8 text-center text-muted-foreground">Loading...</div>;

  return (
    <div className="p-6 max-w-[1400px] mx-auto space-y-8">
      {/* Page Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold">Roles & Permissions</h1>
          <p className="text-muted-foreground text-sm">Manage company departments, system roles, and their access levels.</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Departments Section */}
        <div className="bg-card text-card-foreground rounded-xl border shadow-sm flex flex-col h-full">
          <div className="p-6 border-b flex justify-between items-center">
            <h5 className="font-semibold text-lg">Departments</h5>
            <Button size="sm" onClick={() => setIsDeptModalOpen(true)}>
              <Plus className="w-4 h-4 me-2" /> Add Department
            </Button>
          </div>
          <div className="p-0 overflow-x-auto">
            <Table>
              <TableHeader className="bg-muted/50">
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {departments.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={3} className="text-center text-muted-foreground">No departments found</TableCell>
                  </TableRow>
                ) : (
                  departments.map((dept) => (
                    <TableRow key={dept.id}>
                      <TableCell className="font-medium">{dept.name}</TableCell>
                      <TableCell>
                        <Badge variant={dept.status === 1 ? 'default' : 'secondary'} className={dept.status === 1 ? 'bg-emerald-500 hover:bg-emerald-600' : ''}>
                          {dept.status === 1 ? 'Active' : 'Inactive'}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right">
                        <DropdownMenu>
                          <DropdownMenuTrigger render={<Button variant="ghost" size="icon" className="h-8 w-8"><MoreVertical className="h-4 w-4" /></Button>} />
                          <DropdownMenuContent align="end">
                            <DropdownMenuItem className="cursor-pointer text-destructive focus:text-destructive" onClick={() => handleDeleteDepartment(dept.id)}>
                              <Trash2 className="me-2 h-4 w-4" /> Delete
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </div>
        </div>

        {/* Roles Section */}
        <div className="bg-card text-card-foreground rounded-xl border shadow-sm flex flex-col h-full">
          <div className="p-6 border-b flex justify-between items-center">
            <h5 className="font-semibold text-lg">System Roles & Default Access</h5>
            <Button size="sm" onClick={() => setIsRoleModalOpen(true)}>
              <Plus className="w-4 h-4 me-2" /> Add Role
            </Button>
          </div>
          <div className="p-0 overflow-x-auto">
            <Table>
              <TableHeader className="bg-muted/50">
                <TableRow>
                  <TableHead>Role</TableHead>
                  <TableHead className="text-right">Operations</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {roles.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={2} className="text-center text-muted-foreground">No roles found</TableCell>
                  </TableRow>
                ) : (
                  roles.map((role) => (
                    <TableRow key={role.id}>
                      <TableCell>
                        <div className="flex items-center gap-3">
                          <div className="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <Shield className="w-4 h-4" />
                          </div>
                          <span className="font-medium capitalize">{role.name.replace(/_/g, ' ')}</span>
                        </div>
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex items-center justify-end gap-2">
                          <Button 
                            variant="outline" 
                            size="sm"
                            onClick={() => router.push(`/${routeBoxSlug}/permissions`)}
                          >
                            <ShieldCheck className="w-4 h-4 me-2 text-primary" /> Manage Access
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </div>
        </div>
      </div>

      {/* Add Role Modal */}
      <Dialog open={isRoleModalOpen} onOpenChange={setIsRoleModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add Role</DialogTitle>
          </DialogHeader>
          <div className="py-4">
            <Label htmlFor="roleName" className="mb-2 block">Role Name <span className="text-destructive">*</span></Label>
            <Input 
              id="roleName"
              placeholder="e.g. Manager, HR, Editor" 
              value={newRoleName}
              onChange={(e) => setNewRoleName(e.target.value)}
            />
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsRoleModalOpen(false)}>Cancel</Button>
            <Button onClick={handleCreateRole} disabled={isSaving}>
              {isSaving ? 'Creating...' : 'Create Role'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Add Department Modal */}
      <Dialog open={isDeptModalOpen} onOpenChange={setIsDeptModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Create New Department</DialogTitle>
          </DialogHeader>
          <div className="py-4">
            <Label htmlFor="deptName" className="mb-2 block">Department Name <span className="text-destructive">*</span></Label>
            <Input 
              id="deptName"
              placeholder="e.g. Sales, Marketing, IT" 
              value={newDeptName}
              onChange={(e) => setNewDeptName(e.target.value)}
            />
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsDeptModalOpen(false)}>Cancel</Button>
            <Button onClick={handleCreateDepartment} disabled={isSaving}>
              {isSaving ? 'Creating...' : 'Create Department'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
