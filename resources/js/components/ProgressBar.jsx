import React, { useEffect, useState } from 'react';

const ProgressBar = ({ campaignId }) => {
  const [progress, setProgress] = useState(0);

  useEffect(() => {
    if (!window.Echo || !campaignId) return;

    const channel = window.Echo.channel(`campaign.${campaignId}`);

    channel.listen('EmailProgressUpdated', (e) => {
      setProgress(e.progress);
    });

    return () => {
      channel.stopListening('EmailProgressUpdated');
    };
  }, [campaignId]);

  return (
    <div className="max-w-xl mx-auto mt-16 p-6 bg-white rounded shadow">
      <h1 className="text-2xl font-semibold mb-4">Progreso de Envío de Correos</h1>
      <div className="w-full bg-gray-200 rounded overflow-hidden mb-2">
        <div
          className="bg-blue-600 text-white text-center py-1"
          style={{ width: `${progress}%` }}
        >
          {progress}%
        </div>
      </div>
      <p className="text-sm text-gray-600">Enviado {progress}%</p>
    </div>
  );
};

export default ProgressBar;