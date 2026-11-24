import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export default function ApplianceCreate() {
    const { data, setData, post, processing, errors } = useForm({
        customer_name: '',
        customer_phone: '',
        appliance_type: '',
        model_number: '',
        serial_number: '',
        purchase_date: '',
        warranty_expiry: '',
    });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post(route('appliances.store'));
    }

    return (
        <AppLayout breadcrumbs={[{ title: 'Appliances', href: '/appliances' }, { title: 'Register appliance', href: '/appliances/create' }]}>
            <Head title="Register appliance" />
            <div className="max-w-2xl space-y-6 p-4">
                <h1 className="text-2xl font-semibold">Register appliance</h1>
                <form onSubmit={submit} className="grid gap-5">
                    {([
                        ['customer_name', 'Customer name', 'text'],
                        ['customer_phone', 'Customer phone', 'tel'],
                        ['model_number', 'Model number', 'text'],
                        ['serial_number', 'Serial number', 'text'],
                        ['purchase_date', 'Purchase date', 'date'],
                        ['warranty_expiry', 'Warranty expiry', 'date'],
                    ] as const).map(([field, label, type]) => (
                        <div key={field} className="grid gap-2">
                            <Label htmlFor={field}>{label}</Label>
                            <Input
                                id={field}
                                type={type}
                                value={data[field]}
                                onChange={(event) => setData(field, event.target.value)}
                                required
                            />
                            <InputError message={errors[field]} />
                        </div>
                    ))}
                    <div className="grid gap-2">
                        <Label htmlFor="appliance_type">Appliance type</Label>
                        <select
                            id="appliance_type"
                            className="h-10 rounded-md border border-input bg-background px-3"
                            value={data.appliance_type}
                            onChange={(event) => setData('appliance_type', event.target.value)}
                            required
                        >
                            <option value="">Select an appliance type</option>
                            <option value="Major appliances">Major appliances</option>
                            <option value="Small Appliances">Small Appliances</option>
                            <option value="Consumer electronic">Consumer electronic</option>
                        </select>
                        <InputError message={errors.appliance_type} />
                    </div>
                    <div className="flex gap-3">
                        <Button disabled={processing}>Save appliance</Button>
                        <Button asChild variant="outline">
                            <Link href={route('appliances.index')}>Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
