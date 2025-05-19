// resources/js/pages/Dashboard.js
import React from 'react';
import { Head } from '@inertiajs/react';
import ProgressBar from '../components/ProgressBar';

const Dashboard = ({ campaignId }) => {
  return (
    <>
      <Head title="Dashboard" />
      <ProgressBar campaignId={campaignId} />
    </>
  );
};

export default Dashboard;
