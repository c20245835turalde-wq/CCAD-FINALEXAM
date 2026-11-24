import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Appliance = {
    id: number;
    appliance_type: string;
    serial_number: string;
    customer: { name: string };
};

export default function WarrantyClaimCreate({ appliances }: { appliances: Appliance[] }) {
    const { data, setData, post, processing, errors } = useForm({
        appliance_id: '',
        claim_date: '',
        defect_description: '',
        claim_status: 'pending',
        approved_amount: '',
    });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post(route('warranty-claims.store'));
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Warranty claims', href: '/warranty-claims' },
                { title: 'File claim', href: '/warranty-claims/create' },
            ]}
        >
            <Head title="File warranty claim" />
            <div className="max-w-2xl space-y-6 p-4">
                <h1 className="text-2xl font-semibold">File warranty claim</h1>
                <form onSubmit={submit} className="grid gap-5">
                    <div className="grid gap-2">
                        <Label htmlFor="appliance_id">Appliance</Label>
                        <select
                            id="appliance_id"
                            className="border-input bg-background h-10 rounded-md border px-3"
                            value={data.appliance_id}
                            onChange={(event) => setData('appliance_id', event.target.value)}
                            required
                        >
                            <option value="">Select an appliance</option>
                            {appliances.map((appliance) => (
                                <option key={appliance.id} value={appliance.id}>
                                    {appliance.customer.name} — {appliance.appliance_type} ({appliance.serial_number})
                                </option>
                            ))}
                        </select>
                        <InputError message={errors.appliance_id} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="claim_date">Claim date</Label>
                        <Input
                            id="claim_date"
                            type="date"
                            value={data.claim_date}
                            onChange={(event) => setData('claim_date', event.target.value)}
                            required
                        />
                        <InputError message={errors.claim_date} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="defect_description">Defect description</Label>
                        <textarea
                            id="defect_description"
                            className="border-input bg-background min-h-28 rounded-md border px-3 py-2"
                            value={data.defect_description}
                            onChange={(event) => setData('defect_description', event.target.value)}
                            required
                        />
                        <InputError message={errors.defect_description} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="claim_status">Claim status</Label>
                        <select
                            id="claim_status"
                            className="border-input bg-background h-10 rounded-md border px-3"
                            value={data.claim_status}
                            onChange={(event) => setData('claim_status', event.target.value)}
                            required
                        >
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <InputError message={errors.claim_status} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="approved_amount">Approved amount (required for an eligible approved claim)</Label>
                        <Input
                            id="approved_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            value={data.approved_amount}
                            onChange={(event) => setData('approved_amount', event.target.value)}
                        />
                        <InputError message={errors.approved_amount} />
                    </div>
                    <div className="flex gap-3">
                        <Button disabled={processing || appliances.length === 0}>Save claim</Button>
                        <Button asChild variant="outline">
                            <Link href={route('warranty-claims.index')}>Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
