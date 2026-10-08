const fs = require('fs');
const file = '/Applications/XAMPP/xamppfiles/htdocs/newbizzcrm_v3/frontend/src/app/[box_slug]/(pages)/shift/page.tsx';
let content = fs.readFileSync(file, 'utf8');

content = content.replace(
  /\{\/\* Effective Period \*\/\}[\s\S]*?\{\/\* Short Break 1 \*\/\}/g,
  `{/* Effective Period */}
        <Card>
          <CardHeader className="py-2">
            <CardTitle className="text-base">Effective Period</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-4 pb-4">
            <div className="space-y-1">
              <Label className="text-xs">Start From</Label>
              <Input name="start_from_date" type="date" value={form.start_from_date} onChange={handleInputChange} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">End Date</Label>
              <Input name="end_date" type="date" value={form.end_date} onChange={handleInputChange} className="h-8 text-xs" />
            </div>
          </CardContent>
        </Card>

        {/* Rest Time Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 py-3">
            <CardTitle className="text-base">Rest Time</CardTitle>
            <div className="flex items-center space-x-2">
              <Label className="text-xs">Disable</Label>
              <Switch checked={restEnabled} onCheckedChange={handleRestToggle} className="scale-75" />
              <Label className="text-xs">Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-2 pb-4">
            <div className="space-y-1">
              <Label className="text-xs">Lunch Out</Label>
              <Input name="lunch_out" type="time" value={form.lunch_out} onChange={handleInputChange} disabled={!restEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">Lunch In</Label>
              <Input name="lunch_in" type="time" value={form.lunch_in} onChange={handleInputChange} disabled={!restEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1 col-span-2">
              <Label className="text-xs">Hrs.</Label>
              <Input name="lunch_hours" value={form.lunch_hours} readOnly className="bg-slate-50 cursor-not-allowed h-8 text-xs" />
            </div>
          </CardContent>
        </Card>

        {/* Short Break 1 */}`
);

content = content.replace(
  /\{\/\* Short Break 1 \*\/\}[\s\S]*?\{\/\* Short Break 2 \*\/\}/g,
  `{/* Short Break 1 */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 py-3">
            <CardTitle className="text-base">Short Break 1</CardTitle>
            <div className="flex items-center space-x-2">
              <Label className="text-xs">Disable</Label>
              <Switch checked={shortBreakEnabled} onCheckedChange={handleShortBreakToggle} className="scale-75" />
              <Label className="text-xs">Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-2 pb-4">
            <div className="space-y-1">
              <Label className="text-xs">Break Out</Label>
              <Input name="short_break_out" type="time" value={form.short_break_out} onChange={handleInputChange} disabled={!shortBreakEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">Break In</Label>
              <Input name="short_break_in" type="time" value={form.short_break_in} onChange={handleInputChange} disabled={!shortBreakEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1 col-span-2">
              <Label className="text-xs">Hrs.</Label>
              <Input name="short_break_hours" value={form.short_break_hours} readOnly className="bg-slate-50 cursor-not-allowed h-8 text-xs" />
            </div>
          </CardContent>
        </Card>

        {/* Short Break 2 */}`
);

content = content.replace(
  /\{\/\* Short Break 2 \*\/\}[\s\S]*?<\/div>\s*<div className=\{step === 2/g,
  `{/* Short Break 2 */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 py-3">
            <CardTitle className="text-base">Short Break 2</CardTitle>
            <div className="flex items-center space-x-2">
              <Label className="text-xs">Disable</Label>
              <Switch checked={shortBreak2Enabled} onCheckedChange={handleShortBreak2Toggle} className="scale-75" />
              <Label className="text-xs">Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-2 pb-4">
            <div className="space-y-1">
              <Label className="text-xs">Break Out</Label>
              <Input name="short_break_out_2" type="time" value={form.short_break_out_2} onChange={handleInputChange} disabled={!shortBreak2Enabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">Break In</Label>
              <Input name="short_break_in_2" type="time" value={form.short_break_in_2} onChange={handleInputChange} disabled={!shortBreak2Enabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1 col-span-2">
              <Label className="text-xs">Hrs.</Label>
              <Input name="short_break_hours_2" value={form.short_break_hours_2} readOnly className="bg-slate-50 cursor-not-allowed h-8 text-xs" />
            </div>
          </CardContent>
        </Card>

        {/* Half Day */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 py-3">
            <CardTitle className="text-base">Half Day</CardTitle>
            <div className="flex items-center space-x-2">
              <Label className="text-xs">Disable</Label>
              <Switch checked={halfDayEnabled} onCheckedChange={handleHalfDayToggle} className="scale-75" />
              <Label className="text-xs">Enable</Label>
            </div>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-2 pb-4">
            <div className="space-y-1">
              <Label className="text-xs">Start Time</Label>
              <Input name="half_day_start_time" type="time" value={form.half_day_start_time} onChange={handleInputChange} disabled={!halfDayEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1">
              <Label className="text-xs">End Time</Label>
              <Input name="half_day_end_time" type="time" value={form.half_day_end_time} onChange={handleInputChange} disabled={!halfDayEnabled} className="h-8 text-xs" />
            </div>
            <div className="space-y-1 col-span-2">
              <Label className="text-xs">Hrs.</Label>
              <Input name="half_day_hours" value={form.half_day_hours} readOnly className="bg-slate-50 cursor-not-allowed h-8 text-xs" />
            </div>
          </CardContent>
        </Card>
        </div>
        
        <div className={step === 2`
);

fs.writeFileSync(file, content);
