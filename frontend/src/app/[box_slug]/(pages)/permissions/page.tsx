'use client';

import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { toast } from 'sonner';
import { apiClient } from '@/lib/api-client';
import { useParams } from 'next/navigation';
import { PermissionsTable } from '../employee-list/permissions-table';

export default function RolesPermissionsSettingsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [isLoadingEntities, setIsLoadingEntities] = useState(true);
  
  const [roles, setRoles] = useState<any[]>([]);
  const [departments, setDepartments] = useState<any[]>([]);
  const [teams, setTeams] = useState<any[]>([]);

  // Selected Entity State
  const [selectedRoleType, setSelectedRoleType] = useState<string>('admin'); // 'admin' or 'employee'
  const [selectedDeptId, setSelectedDeptId] = useState<string>('all');
  const [selectedTeamId, setSelectedTeamId] = useState<string>('all');

  const [assignedPermissions, setAssignedPermissions] = useState<number[]>([]);
  const [isSaving, setIsSaving] = useState(false);
  const [isLoadingPermissions, setIsLoadingPermissions] = useState(false);

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    fetchEntities();
  }, [routeBoxSlug]);

  useEffect(() => {
    if (!isLoadingEntities && roles.length > 0) {
      fetchPermissionsForSelection();
    }
  }, [selectedRoleType, selectedDeptId, selectedTeamId, roles]);

  const fetchEntities = async () => {
    try {
      const res = await apiClient.get(`/${routeBoxSlug}/settings/roles-permissions/entities`);
      if (res?.success) {
        setRoles(res.roles);
        setDepartments(res.departments);
        setTeams(res.teams);
      }
    } catch (err: any) {
      toast.error('Failed to load roles and entities');
    } finally {
      setIsLoadingEntities(false);
    }
  };

  const fetchPermissionsForSelection = async () => {
    setIsLoadingPermissions(true);
    try {
      let roleId = '';
      let deptId = '';
      let teamId = '';

      if (selectedRoleType === 'admin') {
        const adminRole = roles.find(r => r.name === 'admin');
        if (adminRole) roleId = adminRole.id;
      } else {
        const empRole = roles.find(r => r.name === 'employee');
        if (empRole && selectedDeptId === 'all' && selectedTeamId === 'all') {
           roleId = empRole.id;
        } else {
           if (selectedDeptId !== 'all') deptId = selectedDeptId;
           if (selectedTeamId !== 'all') teamId = selectedTeamId;
        }
      }

      const res = await apiClient.get(`/${routeBoxSlug}/settings/roles-permissions/entities`, {
        params: {
          role_id: roleId,
          department_id: deptId,
          team_id: teamId,
        }
      });
      
      if (res?.success) {
        setAssignedPermissions(res.assigned_permissions || []);
      }
    } catch (error) {
      console.error(error);
      toast.error('Failed to fetch assigned permissions');
    } finally {
      setIsLoadingPermissions(false);
    }
  };

  const handleSave = async () => {
    setIsSaving(true);
    try {
      let roleId = '';
      let deptId = '';
      let teamId = '';

      if (selectedRoleType === 'admin') {
        const adminRole = roles.find(r => r.name === 'admin');
        if (adminRole) roleId = adminRole.id;
      } else {
        const empRole = roles.find(r => r.name === 'employee');
        if (empRole && selectedDeptId === 'all' && selectedTeamId === 'all') {
           roleId = empRole.id;
        } else {
           if (selectedDeptId !== 'all') deptId = selectedDeptId;
           if (selectedTeamId !== 'all') teamId = selectedTeamId;
        }
      }

      const res = await apiClient.post(`/${routeBoxSlug}/settings/roles-permissions/update`, {
        role_id: roleId,
        department_id: deptId,
        team_id: teamId,
        permissions: assignedPermissions
      });

      if (res?.success) {
        toast.success('Permissions saved successfully');
      } else {
        toast.error(res?.message || 'Failed to save permissions');
      }
    } catch (err: any) {
      toast.error(err?.data?.message || err?.message || 'Failed to save permissions');
    } finally {
      setIsSaving(false);
    }
  };

  const filteredTeams = teams.filter(t => selectedDeptId === 'all' || t.department_id.toString() === selectedDeptId);

  return (
    <div className="p-6 max-w-[1200px] mx-auto">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Roles & Permissions Settings</h1>
          <p className="text-muted-foreground text-sm">Configure permissions for roles, departments, and teams.</p>
        </div>
        <Button onClick={handleSave} disabled={isSaving || isLoadingPermissions}>
          {isSaving ? 'Saving...' : 'Save Permissions'}
        </Button>
      </div>

      <div className="bg-card text-card-foreground rounded-xl shadow-sm border p-6 mb-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label className="block text-sm font-medium mb-2">Select Role Type</label>
            <Select value={selectedRoleType} onValueChange={(val) => {
              const v = val || 'admin';
              setSelectedRoleType(v);
              if (v === 'admin') {
                setSelectedDeptId('all');
                setSelectedTeamId('all');
              }
            }}>
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select Role" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="admin">Admin</SelectItem>
                <SelectItem value="employee">Employee</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {selectedRoleType === 'employee' && (
            <>
              <div>
                <label className="block text-sm font-medium mb-2">Filter by Department</label>
                <Select value={selectedDeptId} onValueChange={(val) => {
                  const v = val || 'all';
                  setSelectedDeptId(v);
                  setSelectedTeamId('all'); // Reset team when department changes
                }}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="All Departments" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">-- All Departments --</SelectItem>
                    {departments.map((d) => (
                      <SelectItem key={d.id} value={d.id.toString()}>{d.name}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Filter by Team / Designation</label>
                <Select value={selectedTeamId} onValueChange={(val) => setSelectedTeamId(val || 'all')}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="All Teams" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">-- All Teams --</SelectItem>
                    {filteredTeams.map((t) => (
                      <SelectItem key={t.id} value={t.id.toString()}>{t.name}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </>
          )}
        </div>
      </div>

      <div className={isLoadingPermissions ? 'opacity-50 pointer-events-none' : ''}>
        <PermissionsTable 
          value={assignedPermissions} 
          onChange={(newPerms) => setAssignedPermissions(newPerms)} 
        />
      </div>
    </div>
  );
}
